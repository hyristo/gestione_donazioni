<?php
//define("DBMS", "Oracle");
define("DBMS_SAFE", false);//PgSqlSafe
define("DBMS_SIAN", false);//PgSqlSian
define("DBMS", "MySql");
define("DBMS_SPID", "MySql");
if (DEV) {    
    if(DBMS == "PgSql"){
        define ("PGSQL_HOST", "192.168.1.120");
        //define ("PGSQL_HOST", "localhost");
        define ("PGSQL_PORT","5432");
        define ("DB_SERVER", PGSQL_HOST.";port=".PGSQL_PORT);
        //define ("DB_SERVER", PGSQL_HOST);
        define ("DB_NAME", "QDC");
        define ("DB_USER", "postgres");
        define ("DB_PASS", "C3nt0s2019");
    }elseif(DBMS == "MySql"){
        define ("MYSQL_HOST", "mysql");
        //define ("MYSQL_HOST", "localhost");
        define ("MYSQL_PORT","3306");
        define ("DB_SERVER", MYSQL_HOST.";port=".MYSQL_PORT);        
        //define ("DB_SERVER", MYSQL_HOST);        
        define ("DB_NAME", "card");
        define ("DB_USER", "root");
        define ("DB_PASS", "root");
    }
    if(DBMS_SPID == "PgSql"){
        define ("PGSQL_HOST", "192.168.1.120");        
        define ("PGSQL_PORT_SPID","5432");
        define ("DB_SERVER_SPID", PGSQL_HOST.";port=".PGSQL_PORT_SPID);
        //define ("DB_SERVER_SPID", PGSQL_HOST);
        define ("DB_NAME_SPID", "QDC");
        define ("DB_USER_SPID", "postgres");
        define ("DB_PASS_SPID", "C3nt0s2019");
    }elseif(DBMS_SPID == "MySql"){
        define ("MYSQL_HOST_SPID", "mysql");
        //define ("PGSQL_HOST", "localhost");
        define ("MYSQL_PORT_SPID","3306");
        define ("DB_SERVER_SPID", MYSQL_HOST_SPID.";port=".MYSQL_PORT_SPID);        
        define ("DB_NAME_SPID", "card");
        define ("DB_USER_SPID", "root");
        define ("DB_PASS_SPID", "root");
    }
    if(DBMS_SIAN){
        define ("PGSQL_HOST_SIAN", "131.1.221.236");        
        define ("PGSQL_PORT_SIAN","5432");
        //define ("DB_SERVER_SIAN", PGSQL_HOST_SIAN);
        define ("DB_SERVER_SIAN", PGSQL_HOST_SIAN.";port=".PGSQL_PORT_SIAN);
        define ("DB_NAME_SIAN", "sian");
        define ("DB_USER_SIAN", "safe");//r2dsvil //safe
        define ("DB_PASS_SIAN", "s0f301");//r2dsv1l //s0f301
    }
    if(DBMS_SAFE){
        define ("PGSQL_HOST_SAFE", "192.168.1.137");
        define ("PGSQL_PORT_SAFE","5432");
        //define ("DB_SERVER_SAFE", PGSQL_HOST_SAFE);
        define ("DB_SERVER_SAFE", PGSQL_HOST_SAFE.";port=".PGSQL_PORT_SAFE);
        define ("DB_NAME_SAFE", "safe");
        define ("DB_USER_SAFE", "postgres");//r2dsvil
        define ("DB_PASS_SAFE", "micro*2019");//r2dsv1l
    }

    
} else {    
    if(DBMS_SPID == "PgSql"){
        define ("PGSQL_HOST", "192.168.1.120");        
        define ("PGSQL_PORT_SPID","5432");
        define ("DB_SERVER_SPID", PGSQL_HOST.";port=".PGSQL_PORT_SPID);
        //define ("DB_SERVER_SPID", PGSQL_HOST);
        define ("DB_NAME_SPID", "QDC");
        define ("DB_USER_SPID", "postgres");
        define ("DB_PASS_SPID", "C3nt0s2019");
    }elseif(DBMS_SPID == "MySql"){
        define ("MYSQL_HOST_SPID", "mysql");
        //define ("PGSQL_HOST", "localhost");
        define ("MYSQL_PORT_SPID","3306");
        define ("DB_SERVER_SPID", MYSQL_HOST_SPID.";port=".MYSQL_PORT_SPID);        
        define ("DB_NAME_SPID", "card");
        define ("DB_USER_SPID", "root");
        define ("DB_PASS_SPID", "root");
    }
}
?>