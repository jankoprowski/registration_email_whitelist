<?php
namespace Drupal\registration_email_whitelist\Service;

use \Drupal\Core\Database\Connection;

class EmailService {

    protected $database;

    public function __construct(Connection $database) {
        $this->database = $database;
    }

    public function getEmailFromWhitelist(String $email) {
        return $this->database->query("SELECT email FROM {registration_email_whitelist} WHERE email = :email", [':email' => $email])->fetchField();
    }

}
