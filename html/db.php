<?php

require_once 'aux_func.php';


function reg_client_db($email, $pass) {

	try {
		$con = get_dbconn();

		print_debug_message('Checking if user with same email exists...');
		$email = mysql_real_escape_string($email);
		$query = 'select * from USERS
			  where email="' . $email . '"';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows != 0)
			return array('status' => false, 'err_message' => 'Existing user with same email');

		print_debug_message('No registered user with same email exists. Inserting new user to db...');
		$pass = mysql_real_escape_string($pass);
		$query = 'insert into USERS (email, password)
			  values ("' . $email . '", "' . $pass . '")';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Unsuccesfully stored. Please try again');

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}
	
	return array('status' => true);
}

function login_client_db($email, $pass) {

	try {
		$con = get_dbconn();

		print_debug_message('Checking if credentials were correct...');
		$email = mysql_real_escape_string($email);
		$pass = mysql_real_escape_string($pass);
		$query = 'select * from USERS
			  where email="' . $email . '" and
			  password="' . $pass . '" and
			  is_employee=0';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Wrong email or password');

		$rec = mysqli_fetch_array($result);
		if ($rec['is_approved'] == 0)
			return array('status' => false, 'err_message' => 'Registration not approved yet');

		print_debug_message('Credentials were correct');

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true);
}

function get_account_client_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining balance of user...');
		$email = mysql_real_escape_string($email);
		$query = 'select balance from BALANCE
			  where email="' . $email . '"';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Email is not registered');
		$row = mysqli_fetch_array($result);
		$balance = $row['balance'];

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true, 'balance' => $balance);
}

function get_trans_client_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining transaction records...');
		$email = mysql_real_escape_string($email);
		$query = 'select trans_id, email_src, email_dest, amount, date, is_approved from TRANSACTIONS
			  where email_src="' . $email . '" order by trans_id';
		$result = mysqli_query($con, $query);

		$trans_recs = array();
		while ($rec = mysqli_fetch_array($result)) {
			$trans_rec = array($rec['trans_id'], $rec['email_dest'], $rec['amount'], $rec['date'], $rec['is_approved']);
			array_push($trans_recs, $trans_rec);
		}

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true, 'trans_recs' => $trans_recs);
}

function get_tancode_id_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining free tan_code_id of user...');
		$email = mysql_real_escape_string($email);
		$query = 'select trans_code_id from TRANSACTION_CODES where email="' . $email . '" and
			  Is_used=0';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'No free tancodes available!');
		$number = rand(0, $num_rows-1);
		if(!mysqli_data_seek($result,$number))
			return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
		$row = mysqli_fetch_array($result);
		$tancode_id = $row['tancode_id'];

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}
	
	return array('status' => true, 'tancode_id' => $tancode_id);
}

function reg_emp_db($email, $pass) {

	try {
		$con = get_dbconn();

		print_debug_message('Checking if user with same email exists...');
		$email = mysql_real_escape_string($email);
		$query = 'select * from USERS
			  where email="' . $email . '"';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows != 0)
			return array('status' => false, 'err_message' => 'Already used email');

		print_debug_message('No registered user with same email exists. Inserting new user to db...');
		$pass = mysql_real_escape_string($pass);
		$query = 'insert into USERS (email, password, is_employee)
			  values ("' . $email . '", "' . $pass . '", 1)';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Unsuccesfully stored. Please try again');

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true);
}

function login_emp_db($email, $pass) {

	try {
		$con = get_dbconn();

		print_debug_message('Checking if credentials were correct...');
		$email = mysql_real_escape_string($email);
		$pass = mysql_real_escape_string($pass);
		$query = 'select * from USERS
			  where email="' . $email . '" and
			  password="' . $pass . '" and
			  is_employee=1';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Wrong email or password');

		$rec = mysqli_fetch_array($result);
		if ($rec['is_approved'] == 0)
			return array('status' => false, 'err_message' => 'Registration not approved yet');

		print_debug_message('Credentials were correct');

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true);
}

function get_client_db() {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining List of all clients...');
		$query = 'select email from USERS
			  where is_employee = 0 and is_approved = 1';
		$result = mysqli_query($con, $query);

		$clients = array();
		while ($rec = mysqli_fetch_array($result)) {
			$client = $rec['email'];
			array_push($clients, $client);
		}

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true, 'clients' => $clients);
}

function get_account_emp_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining balance of user...');
		$email = mysql_real_escape_string($email);
		$query = 'select balance from BALANCE
			  where email="' . $email . '"';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_num_rows($result);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Email is not registered');
		$row = mysqli_fetch_array($result);
		$balance = $row['balance'];

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true, 'balance' => $balance);
}

function get_trans_emp_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining transaction records...');
		$email = mysql_real_escape_string($email);
		$query = 'select trans_id, email_src, email_dest, amount, date, is_approved from TRANSACTIONS
			  where email_src="' . $email . '" order by trans_id';
		$result = mysqli_query($con, $query);

		$trans_recs = array();
		while ($rec = mysqli_fetch_array($result)) {
			$trans_rec = array($rec['trans_id'], $rec['email_dest'], $rec['amount'], $rec['date'], $rec['is_approved']);
			array_push($trans_recs, $trans_rec);
		}

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}
	
	return array('status' => true, 'trans_recs' => $trans_recs);
}

function get_trans_db() {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining unapproved transaction records...');
		$query = 'select trans_id, email_src, email_dest, amount, date from TRANSACTIONS
			  where is_approved=0
			  and amount>=10000
			  order by trans_id';
		$result = mysqli_query($con, $query);

		$trans_recs = array();
		while ($rec = mysqli_fetch_array($result)) {
			$trans_rec = array($rec['trans_id'], $rec['email_src'], $rec['email_dest'], $rec['amount'], $rec['date']);
			array_push($trans_recs, $trans_rec);
		}

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true, 'trans_recs' => $trans_recs);
}

function approve_trans_db($trans_id) {

	try {
		$con = get_dbconn();

		$trans_id = mysql_real_escape_string($trans_id);
		
		$query = 'select email_src,email_dest,amount from TRANSACTIONS
			  where trans_id="' . $trans_id . '"';
		$result = mysqli_query($con, $query);
		$num_rows = mysqli_num_rows($result);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Non existing transaction with the specified id');
		$row = mysqli_fetch_array($result);
		
		$status = transfer_money($row['email_src'],$row['email_dest'],$row['amount'],1);

		if($status == true) {
			$query = 'update TRANSACTIONS set is_approved=1
				  where trans_id="' . $trans_id . '"';
			$result = mysqli_query($con, $query);
			$num_rows = mysqli_affected_rows($con);
			if ($num_rows == 0)
				return array('status' => false, 'err_message' => 'Non existing transaction with the specified id');
		} else
			return;

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
		return 'Something went wrong. Please try again';
	}

	return array('status' => true);
}

function reject_trans_db($trans_id) {

	try {
		$con = get_dbconn();

		$trans_id = mysql_real_escape_string($trans_id);
		$query = 'delete from TRANSACTIONS
			  where trans_id="' . $trans_id . '"
			  and is_approved=0';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Non existing transaction with the specified id');

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true);
}

function get_new_users_db() {

	try {
		$con = get_dbconn();

		print_debug_message('Obtaining new users...');
		$query = 'select email, is_employee from USERS
			  where is_approved=0';
		$result = mysqli_query($con, $query);

		$new_users = array();
		while ($rec = mysqli_fetch_array($result)) {
			$user_type = $rec['is_employee'] == 1 ? 'employee' : 'client';
			$new_user = array($rec['email'], $user_type);
			array_push($new_users, $new_user);
		}

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true, 'new_users' => $new_users);
}

function approve_user_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Approving new user...');
		$email = mysql_real_escape_string($email);
		$query = 'update USERS set is_approved=1
			  where email="' . $email . '" and
			  is_approved=0';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Non existing user with the specified email');

		$query = 'select is_employee from USERS where email ="'.$email.'"';
		$result = mysqli_query($con,$query);
		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Non existing user with the specified email');
		$row = mysqli_fetch_array($result);
		if($row['is_employee'] == 0){
			$codes = array();
			for($i = 0 ; $i < 100 ; $i++){
				$codes[$i]['value'] = uniqid(chr(mt_rand(97,122)).chr(mt_rand(97,122)));
				$query = 'insert into TRANSACTION_CODES (trans_code,email)
				 values ("'.$codes[$i]['value'].'","'.$email.'")';
				$result = mysqli_query($con,$query);
				$num_rows = mysqli_affected_rows($con);
				if ($num_rows == 0)
					return array('status' => false, 'err_message' => 'Whoops, something went wrong while adding tancode');
				$query = "SELECT LAST_INSERT_ID()";
				$result = mysqli_query($con,$query);
				$row = mysqli_fetch_array($result);
				$codes[$i]['ID'] = $row[0];
			}
				
		send_tancodes($codes,$email);
		$query = 'insert into BALANCE (email, balance)
			  values ("' . $email . '", ' . rand(1000, 15000) . ')';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Can"t add money to user!. Please try again');

		}		
		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}

	return array('status' => true);
}

function reject_user_db($email) {

	try {
		$con = get_dbconn();

		print_debug_message('Rejecting new user...');
		$email = mysql_real_escape_string($email);
		$query = 'delete from USERS
			  where email="' . $email . '" and
			  is_approved=0';
		$result = mysqli_query($con, $query);

		$num_rows = mysqli_affected_rows($con);
		if ($num_rows == 0)
			return array('status' => false, 'err_message' => 'Non existing user with the specified email');

		close_dbconn($con);

	} catch (Exception $e) {
		print_debug_message('Exception occured: ' . $e->getMessage());
		return array('status' => false, 'err_message' => 'Something went wrong. Please try again');
	}
	
	return array('status' => true);
}

?>