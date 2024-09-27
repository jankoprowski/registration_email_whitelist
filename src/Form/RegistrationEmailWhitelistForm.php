<?php 

namespace Drupal\registration_email_whitelist\Form {

    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;

    class RegistrationEmailWhitelistForm extends FormBase {

        public function getFormId() {
            return 'registration_email_whitelist_form';
        }

        public function buildForm(array $form, FormStateInterface $form_state) {
            $connection = \Drupal::database();
            $results = $connection->query("SELECT id, email FROM {registration_email_whitelist}")->fetchAll();
            
            $header = ['email' => $this->t('E-mail')];
            $table = array();
            foreach ($results as $result) {
                $table[$result->id] = [
                    'email' => $result->email,
                ];  
            }
            $form['table'] = [
                '#type' => 'tableselect',
                '#header' => $header,
                '#options' => $table,
                '#empty' => $this->t('No e-mails found.')
            ];
            $form['email'] = [
                '#type' => 'email',
                '#title' => $this->t('Email'),
            ];
            $form['add'] = [
                '#type' => 'submit',
                '#value' => $this->t('Add'),
                '#submit' => [[$this, 'add']],
                '#validate' => [[$this, 'validateAdd']],
            ];
            $form['delete'] = [
                '#type' => 'submit',
                '#value' => $this->t('Delete'),
                '#submit' => [[$this, 'delete']],
                '#validate' => [[$this, 'validateDelete']],
            ];
            return $form;
        }

        public function add(array &$form, FormStateInterface $form_state) {
            $connection = \Drupal::service('database');
            $result = $connection->insert('registration_email_whitelist')
            ->fields([
                'email' => $form_state->getValue('email')
            ])
            ->execute();
            $this->messenger()->addStatus($this->t('E-mail %email added succesfully.', [
                '%email' => $form_state->getValue('email'),
              ]));
        }


        public function delete(array &$form, FormStateInterface $form_state) {
            $to_delete = array_filter($form_state->getValues()['table'],
            function($v, $k) {
                return (string)$k === (string)$v;
            }, ARRAY_FILTER_USE_BOTH);
            $to_delete = array_keys($to_delete);

            $connection = \Drupal::database();

            $connection->delete('registration_email_whitelist')
            ->condition('id', $to_delete, 'IN')
            ->execute();
        }

        public function submitForm(array &$form, FormStateInterface $form_state) {
        }

        public function validateAdd(array &$form, FormStateInterface $form_state) {
            $connection = \Drupal::database();
            if (empty($form_state->getValue('email'))) {
                $form_state->setErrorByName('email', $this->t('E-mail can\'t be empty.'));
            }
            $email = $connection->query("SELECT email FROM {registration_email_whitelist} WHERE email = :email", [':email' => $form_state->getValue('email')])->fetchField();
            if ($email === $form_state->getValue('email')) {
                $form_state->setErrorByName(
                    'message',
                    $this->t('E-mail already exists in database.'),
                );
            }
        }

        public function validateDelete(array &$form, FormStateInterface $form_state) {
            
            $to_delete = array_filter($form_state->getValues()['table'],
            function($v, $k) {
                return (string)$k === (string)$v;
            }, mode: ARRAY_FILTER_USE_BOTH);
            $to_delete = array_keys($to_delete);

            if (empty($to_delete)) {
                $form_state->setErrorByName(
                    'message',
                    $this->t('Please select e-mails to delete.'),
                );
            }            
        }


        public function validateForm(array &$form, FormStateInterface $form_state) {
        }
        

    };
}

