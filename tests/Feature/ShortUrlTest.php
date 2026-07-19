<?php

namespace Tests\Feature;
use App\Models\Company;
use App\Models\User;
use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
    }

   private function createCompany(string $name = 'Tata'):Company{
        return Company::create([
            'name'=>$name,
            'slug'=>Str::slug($name).'-'.uniqid()
        ]);
    }

   private function createUser(Company $company, string $role):User{
    return User::factory()->create([
        'company_id' => $company->id,
            'role'       => $role,
    ]);
   }

   private function createSuperAdmin():User{
            return User::factory()->create([
           'company_id' => null,
            'role'       => User::ROLE_SUPERADMIN,
    ]);
   }

   #[Test]
    public function admin_can_create_short_url(): void
    {
        $company = $this->createCompany();
        $admin   = $this->createUser($company, User::ROLE_ADMIN);

        $response = $this->actingAs($admin)->post(route('short-urls.store'), [
            'original_url' => 'https://google.com/some-long-url',
        ]);

        $response->assertRedirect(route('short-urls.index'));
        $this->assertDatabaseHas('short_urls', [
            'user_id'      => $admin->id,
            'company_id'   => $company->id,
            'original_url' => 'https://google.com/some-long-url',
        ]);
    }

    #[Test]
    public function member_can_create_short_url():void{
            $company = $this->createCompany();
            $member  = $this->createUser($company, User::ROLE_MEMBER);

            $response = $this->actingAs($member)->post(route('short-urls.store'), [
            'original_url' => 'https://google.com/member-url',
        ]);

        $response->assertRedirect(route('short-urls.index'));
        $this->assertDatabaseHas('short_urls', [
            'user_id'      => $member->id,
            'company_id'   => $company->id,
            'original_url' => 'https://google.com/member-url',
        ]);
    }

    #[Test]
    public function superadmin_cannot_create_short_url():void{
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->post(route('short-urls.store'), [
            'original_url' => 'https://google.com/super-url',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('short_urls', 0);
 }
    #[Test]
     public function superadmin_cannot_access_create_from():void{
        $superAdmin = $this->createSuperAdmin();
        $response = $this->actingAs($superAdmin)->get(route('short-urls.create'));

        $response->assertForbidden();
     }
    #[Test]
    public function admin_can_only_see_short_url_from_their_company():void{

        $company1 = $this->createCompany('Company1');
        $company2 = $this->createCompany('Company2');
        $admin   = $this->createUser($company1, User::ROLE_ADMIN);
        $member1 = $this->createUser($company1, User::ROLE_MEMBER);
        $member2 = $this->createUser($company2, User::ROLE_MEMBER);

        $urlInCompany  = ShortUrl::factory()->create(['company_id' => $company1->id, 'user_id' => $member1->id]);
        $urlOutCompany = ShortUrl::factory()->create(['company_id' => $company2->id, 'user_id' => $member2->id]);
        $response = $this->actingAs($admin)->get(route('short-urls.index'));

        $response->assertOk();
        $response->assertSee($urlInCompany->code);
        $response->assertDontSee($urlOutCompany->code);

    }
    #[Test]
    public function member_can_only_see_their_own_short_url():void{
        
        $company = $this->createCompany();
        $member1 = $this->createUser($company, User::ROLE_MEMBER);
        $member2 = $this->createUser($company, User::ROLE_MEMBER);
        $ownUrl   = ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $member1->id]);
        $otherUrl = ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $member2->id]);
        $response = $this->actingAs($member1)->get(route('short-urls.index'));
        $response->assertOk();
        $response->assertSee($ownUrl->code);
        $response->assertDontSee($otherUrl->code);
    }

    #[Test]
    public function superadmin_can_see_all_short_urls():void{
        
        $company1   = $this->createCompany('Company1');
        $company2   = $this->createCompany('Company2');
        $superAdmin = $this->createSuperAdmin();
        $member1 = $this->createUser($company1, User::ROLE_MEMBER);
        $member2 = $this->createUser($company2, User::ROLE_MEMBER);
        $url1 = ShortUrl::factory()->create(['company_id' => $company1->id, 'user_id' => $member1->id]);
        $url2 = ShortUrl::factory()->create(['company_id' => $company2->id, 'user_id' => $member2->id]);
        $response = $this->actingAs($superAdmin)->get(route('short-urls.index'));
        $response->assertOk();
        $response->assertSee($url1->code);
        $response->assertSee($url2->code);
    }

    #[Test]
    public function short_url_redirects_to_original_url():void{

        $company  = $this->createCompany();
        $member   = $this->createUser($company, User::ROLE_MEMBER);
        $shortUrl = ShortUrl::factory()->create([
            'company_id'   => $company->id,
            'user_id'      => $member->id,
            'original_url' => 'https://www.google.com/target',
            'code'         => 'abc123',
        ]);
         $response = $this->get(route('short-urls.redirect', $shortUrl->code));

        $response->assertRedirect('https://www.google.com/target');    
    }
    #[Test]
    public function short_url_redirect_is_public_accessable_without_login() :void{
       
        $company  = $this->createCompany();
        $member   = $this->createUser($company, User::ROLE_MEMBER);
        $shortUrl = ShortUrl::factory()->create([
            'company_id'   => $company->id,
            'user_id'      => $member->id,
            'original_url' => 'https://www.google.com/public',
            'code'         => 'pub999',
        ]);
        $response = $this->get(route('short-urls.redirect', $shortUrl->code));
        $response->assertRedirect('https://www.google.com/public');
    }

    #[Test]
    public function short_url_returns_404_for_unknown_code(): void
    {
        $response = $this->get('/s/notexist_url');
        $response->assertNotFound();
    }
}
