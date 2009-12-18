<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2009
 */

class User_Models_Forms_Update extends Zend_Form {
	const PASSWORD_MAX = 10;
	const PASSWORD_MIN = 6;
	function __construct($user){
		parent::__construct();
		$this->setName('Registration');
		$this->setMethod('POST');
		$this->setAction('/user/update');

		$gender = new Zend_Form_Element_Select('gender');
		$gender->setLabel('Gender');
		$gender->setMultiOptions(array('Male' => 'Male', 'Female' => 'Female'));
		$gender->setValue($user->gender);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email');
		$email->setValue($user->email);
		$email->setRequired(true);
		$email->addValidator('NotEmpty',true);
		$email->addValidator(new User_Models_Forms_Validators_EmailAddress(),true);

		$paymentEmail = new Zend_Form_Element_Text('paymentEmail');
		$paymentEmail->setLabel('Payment Email');
		$paymentEmail->setRequired(true);
		$paymentEmail->addValidator('NotEmpty', true);
		$paymentEmail->addValidator(new User_Models_Forms_Validators_EmailAddress(), true);
		$paymentEmail->setValue($user->paymentEmail);

		$countries = new Zend_Form_Element_Select('country');
		$countries->setMultiOptions(self::getCountries());
		$countries->setLabel('Country');
		$countries->addValidator('NotEmpty');
		$countries->setValue($user->country);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Update');

		$this->addElements(array($gender, $email,$paymentEmail,$countries,$submit));
	}

	/**
	 * Reads the countries that the user is allowed to register from a text file
	 * The  file is located at APPLICATION_PATH . /modules/user/models/countries.txt
	 * The name of each country must be on a new line
	 * @return array $countries
	 */
	public static function getCountries() {
		$lines = file(APPLICATION_PATH. '/modules/user/models/countries.txt');
		$countries = array();
		foreach($lines as $line) {
			$line = rtrim($line);
			$countries[$line] = $line;
		}
		return $countries;
	}
}
?>