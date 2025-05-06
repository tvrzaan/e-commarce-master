<?php
abstract class user{
    public $user_id ;
    public $username;
    public $email;
    public $phone;
    protected $password;
    public $address;
    public $gender;
    public $birthday;
    public $created_at;
    public $updated_at;
    function __construct($user_id,$username,$email,$phone,$password,$address,$gender,$birthday,$created_at,$updated_at){
      $this-> user_id=$user_id ;
      $this-> username=$username;
      $this-> email=$email;
      $this-> phone=$phone;
      $this-> password=$password;
      $this-> address=$address;
      $this-> gender=$gender;
      $this-> birthday=$birthday;
      $this-> created_at=$created_at;
      $this-> updated_at=$updated_at;






    }
    public static function login($email,$password){
        $user=null ;
        $qry ="SELECT * FROM users WHERE email='$email'AND password='$password'";
        require_once('config.php');
        $cn = mysqli_connect(db_host, db_username, db_pw, db_name);
        $rsl = mysqli_query($cn, $qry);
        
        if($arr= mysqli_fetch_assoc($rsl)){
            switch ($arr["role"]) {
                case 'n_user':
                    $user= new n_user($arr["user_id"],$arr["username"],$arr["email"],$arr["password"],$arr["phone"],$arr["address"],$arr["created_at"],$arr["updated_at"],$arr["gender"],$arr["birthday"]);
                    
                    break;
                
                    case 'admin':
                    $user= new admin($arr["user_id"],$arr["username"],$arr["email"],$arr["password"],$arr["phone"],$arr["address"],$arr["created_at"],$arr["updated_at"],$arr["gender"],$arr["birthday"]);
                    
                        break;
                    
            }
            
        }
        mysqli_close($cn);
        return $user;
        

    }
}
//class n_user extends user {

//public $role = 'user';
//public static function register($firstName,$lastname,$email,$phoneNumber,$adress,$password,$gender,$birthday){
//    $qry = "INSERT INTO users (username,email,phone,adress,password,gender,birthday) VALUES ()"
//    require_once ('config.php');
//    $cn = mysqli_connect('localhost','root','','ecommerce_db');
//    var_dump($cn);
//}
class n_user extends user {

    public $role = 'n_user';

    public static function register($username, $email, $phoneNumber, $adress, $password, $gender, $birthday) {
        require_once('config.php');

        $cn = mysqli_connect(db_host, db_username, db_pw, db_name);

       

       

        $qry = "INSERT INTO users (username, email, phone, address, password, gender, birthday) 
        VALUES ('$username', '$email', '$phoneNumber', '$adress', '$password', '$gender', '$birthday');";

         $rsl = mysqli_query($cn, $qry);
         mysqli_close($cn);
         return $rsl;

    }
}





class admin extends user{
   
    public $role = 'admin';
    
    
}

?>