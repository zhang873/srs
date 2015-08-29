<h1>{{ Lang::get('user/user.admission_confirmation_subject') }}</h1>

<p>{{ Lang::get('user/user.admission_confirmation_greetings', array('name' => $admission['fullname'])) }},</p>

<p>{{ Lang::get('user/user.admission_confirmation_body', array('id' => $admission['admissionid'])) }}</p>

<p>{{ Lang::get('user/user.admission_confirmation_farewell') }}</p>
