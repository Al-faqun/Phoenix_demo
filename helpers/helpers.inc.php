<?php
    include_once('__php__.php');
    /**
     * Вспомогательные классы и функции
     */
    
    /** 
     * Включает сессию, если она ещё не включена, тем самым избегаем лишних сообщений.
     * 
     * @return void
     */
    function session_true_start() 
    {
        $status = session_status();
        if ($status == PHP_SESSION_NONE) {
            //There is no active session
            session_start();
        } else
            
        if ($status == PHP_SESSION_DISABLED) {
            echo 'Sessions are not available! Exiting.';
            exit();
		} else
            
		if ($status == PHP_SESSION_ACTIVE) {
	    //ничего не делать, потому что отключение сессии удалит связанные с ней данные
		}
    }
	
?>