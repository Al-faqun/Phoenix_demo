<?php
namespace Shinoa;
class Helpers 
{
    /** 
     * Включает сессию, если она ещё не включена, тем самым избегаем лишних сообщений.
     * 
     * @return void
     */
    public static function sessionTrueStart() 
    {
        $status = session_status();
        if ($status == PHP_SESSION_NONE) {
            //There is no active session
            session_start();
        } else
            
        if ($status == PHP_SESSION_DISABLED) {
            throw new Exception('Sessions are not available! Exiting.');
		} else
            
		if ($status == PHP_SESSION_ACTIVE) {
	    //ничего не делать, потому что отключение сессии удалит связанные с ней данные
		}
    }
}	
