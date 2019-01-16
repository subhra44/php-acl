<?php

class Acl
{
    private static $conn = null;

    function __construct()
    {
        // Load environment variables
        require __DIR__ . '/vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::create(__DIR__);
        $dotenv->load();

        $server = getenv('DB_DRIVER').":host=".getenv('DB_HOST').";dbname=".getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');
        $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ);

        try
        {
            self::$conn = new PDO($server, $user, $pass, $options);
        }
        catch (PDOException $e)
        {
            die("Error connecting to DB: " . $e->getMessage());
        }
    }

    /**
     * Create a new user groups with different privileges.
     *
     * @param array $role Information about the role to be created.
     * @param array $permissions Array of permissions to be assigned to the role.
     * @return int id of the newly created role.
     */
    public function createRole($role, $permissions)
    {
        $sql = "INSERT INTO roles ( name ) VALUES ( ? )";
        $insert_values = array(
            $role['name']
        );

        try
        {
            $stmt = self::$conn->prepare( $sql ) ;
            $stmt->execute( $insert_values );

            $role_id = self::$conn->lastInsertId();
        }
        catch(PDOException $e)
        {
            die("Error inserting role: " . $e->getMessage());
        }

        $clause = implode(',', array_fill(0, count($permissions), '?'));
        $stmt = self::$conn->prepare("SELECT id FROM permissions WHERE permission IN ($clause)");
        $stmt->execute( $permissions );
        $permission_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if(!$permission_ids)
        {
            die("Permissions not found");
        }
        
        $insert_values = array();
        foreach($permission_ids as $permission_id) {
            $question_marks[] = '(?, ?)';
            $insert_values = array_merge($insert_values, array(
                $permission_id,
                $role_id
            ));
        }

        $sql = "INSERT INTO permission_role ( permission_id, role_id ) VALUES " . implode( ', ', $question_marks );
        try
        {
            $stmt = self::$conn->prepare($sql);
            $stmt->execute($insert_values);
        }
        catch (PDOException $e)
        {
            die("Error inserting permissions: " . $e->getMessage());
        }

        self::$conn = null;
        return $role_id;
    }

    /**
     * Create a new users with each user belonging to a user group.
     *
     * @param array $user Information about the user to be created.
     * @param array $roles Array of roles to be assigned to the user.
     * @return int ID of the newly created user.
     */
    public function createUser($user, $roles)
    {
        $sql = "INSERT INTO users ( name ) VALUES ( ? )";
        $insert_values = array(
            $user['name']
        );

        try
        {
            $stmt = self::$conn->prepare( $sql ) ;
            $stmt->execute( $insert_values );

            $user_id = self::$conn->lastInsertId();
        }
        catch(PDOException $e)
        {
            die("Error inserting user: " . $e->getMessage());
        }

        $clause = implode(',', array_fill(0, count($roles), '?'));
        $stmt = self::$conn->prepare("SELECT id FROM roles WHERE name IN ($clause)");
        $stmt->execute( $roles );
        $role_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if(!$role_ids)
        {
            die("Roles not found");
        }
        
        $insert_values = array();
        foreach($role_ids as $role_id) {
            $question_marks[] = '(?, ?)';
            $insert_values = array_merge($insert_values, array(
                $user_id,
                $role_id
            ));
        }

        $sql = "INSERT INTO role_user ( user_id, role_id ) VALUES " . implode( ', ', $question_marks );
        try
        {
            $stmt = self::$conn->prepare($sql);
            $stmt->execute($insert_values);
        }
        catch (PDOException $e)
        {
            die("Error inserting user roles: " . $e->getMessage());
        }

        self::$conn = null;
        return $user_id;
    }

    /**
     * Assign a user to report to one or more users.
     *
     * @param int $user_id ID of a user.
     * @param array $reportee_ids Array of user IDs to be assigned to the user as reportees.
     * @return Boolean TRUE if assigned successfully.
     */
    public function assignUser($user_id, $reportee_ids)
    {   
        $insert_values = array();
        foreach($reportee_ids as $reportee_id) {
            $question_marks[] = '(?, ?)';
            $insert_values = array_merge($insert_values, array(
                $user_id,
                $reportee_id
            ));
        }

        $sql = "INSERT INTO user_structure ( user_id, reports_to ) VALUES " . implode( ', ', $question_marks );
        try
        {
            $stmt = self::$conn->prepare($sql);
            $stmt->execute($insert_values);

            self::$conn = null;
            return true;
        }
        catch (PDOException $e)
        {
            error_log("Error inserting user roles: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find the list of all users who report to the current user
     *
     * @param int $user_id ID of a user.
     * @return array Users who report to the current user.
     */
    public function getUser($user_id)
    {
        $stmt = self::$conn->prepare("SELECT users.name FROM users JOIN user_structure ON users.id = user_structure.user_id WHERE user_structure.reports_to = ?");
        $stmt->execute( array($user_id) );
        $users = $stmt->fetchAll();

        self::$conn = null;
        return $users;
    }

    /**
     * Find the list of all users who the current user reports to
     *
     * @param int $user_id ID of a user.
     * @return array Users to whom the current user reports.
     */
    public function getReportees($user_id)
    {
        $stmt = self::$conn->prepare("SELECT users.name FROM users JOIN user_structure ON users.id = user_structure.reports_to WHERE user_structure.user_id = ?");
        $stmt->execute( array($user_id) );
        $reportees = $stmt->fetchAll();

        self::$conn = null;
        return $reportees;
    }

    /**
     * Find the privileges of the current user based on the privileges of the user group
     *
     * @param int $user_id ID of a user.
     * @return array Permissions of assigned to the current user.
     */
    public function getPermissions($user_id)
    {
        $stmt = self::$conn->prepare("SELECT permissions.permission FROM permissions JOIN permission_role ON permission_role.permission_id = permissions.id JOIN role_user ON role_user.role_id = permission_role.role_id WHERE role_user.user_id = ?");
        $stmt->execute( array($user_id) );
        $permissions = $stmt->fetchAll();

        self::$conn = null;
        return $permissions;
    }    
}