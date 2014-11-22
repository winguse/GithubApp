<?php
abstract class BasicUser{
    public $grade;
    public $real_name;
    public $student_id;
    public $sex;
    public $major_id;
    public $email;
}

class User extends BasicUser {
    public $id;
    public $role;
    public $github_id;
    public $github_login;
    public $github_name;
    public $github_location;
    public $github_created_at;
    public $github_updated_at;
    public $github_access_token;
}
