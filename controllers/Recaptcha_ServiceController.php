<?php
namespace Craft;

/**
 * Recaptcha controller
 */
class Recaptcha_ServiceController extends BaseController
{
    protected $allowAnonymous = true;
    /**
     * Handle the save user form request.
     */
    public function actionSaveUser()
    {
        $this->requirePostRequest();
        $captcha = craft()->request->getPost('g-recaptcha-response');
        $verified = craft()->recaptcha_verify->verify($captcha);

        if ($verified) {
            $this->forward('users/saveUser', false);
        } else {
            $user = new UserModel();
            $user->addError('recaptcha', Craft::t("Failed reCAPTCHA validation."));

            // Store given values, so the user don't has to repeat them
            $user->username = craft()->request->getPost('username');
            $user->email = craft()->request->getPost('email');

            // Send the account back to the template
            craft()->urlManager->setRouteVariables(array(
                'account' => $user
            ));
        }
    }
    
    /**
     * Handle the Craft Contact form plugin form request.
     */
    public function actionContactFormSendMessage()
    {
        $this->requirePostRequest();
        
        $captcha = craft()->request->getPost('g-recaptcha-response');
            
        $verified = craft()->recaptcha_verify->verify($captcha);
        
        if ($verified) {
            $majorVersion = AppHelper::getMajorVersion(craft()->getVersion());

            if ($majorVersion === 3) {
                $this->forward('contact-form/send', false);
            } else if ($majorVersion === 2) {
                $this->forward('contactForm/sendMessage', false);
            } 
        } else {
            throw new Exception(Craft::t('Failed reCAPTCHA validation.'));
        }
    }
}
