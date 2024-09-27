<?php

namespace Drupal\Tests\registration_email_whitelist\Functional;

use Drupal\Tests\BrowserTestBase;

class AdminRegistrationEmailWhitelistTest extends BrowserTestBase {

    protected $defaultTheme = 'stark';

    protected static $modules = ['registration_email_whitelist'];
    

    public function testAddingEmailToWhitelist() {  

        $user = $this->drupalCreateUser(['administer site configuration']);
        $this->drupalLogin($user);

        $mail = $this->randomMachineName() . '@example.com';
        $edit = [
            'email' => $mail,
        ];

        $this->drupalGet('admin/config/registration-email-whitelist');
        $this->submitForm($edit, 'Add');

        $this->assertSession()->statusCodeEquals(200);
        $this->assertSession()->responseContains('E-mail <em class="placeholder">' . $mail . '</em> added succesfully.');
    }

    public function testRegistrationOutsideTheEmailWhitelist() {
    
        $mail = $this->randomMachineName() . '@example.com';
        
        $edit = [];
        $edit['name'] = $this->randomMachineName();
        $edit['mail'] = $mail;

        // Attempt to create a new account using an existing email address.
        $this->drupalGet('user/register');
        $this->submitForm($edit, 'Create new account');

        $this->assertSession()->pageTextContains('E-mail is not on the white list.');
    }
}