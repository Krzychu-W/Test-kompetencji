<?php
/**
 * Message.php.
 */
/**
 * <strong>Klasa do wyświetlania oraz przechowywania komunikatów</strong>.
 *
 * @author Krzysztof Wałek
 */
class qMessage
{
    /**
     * Wyświetla informację.
     *
     * @param string $message Komunikat (=false)
     */
    public static function info($message)
    {
        self::add('message-info', $message);
    }

    /**
     * Wyświetla komunikat sukcesu.
     *
     * @param string $message Komunikat (=false)
     */
    public static function success($message)
    {
        self::add('message-success', $message);
    }

    /**
     * Wyświetla błąd.
     *
     * @param string $message Komunikat (=false)
     */
    public static function error($message)
    {
        self::add('message-error', $message);
    }

    /**
     * Wyświetla ostrzeżenie.
     *
     * @param string $message Komunikat (=false)
     */
    public static function warning($message)
    {
        self::add('message-warning', $message);
    }

    public static function add($type, $message) {
        $aMessage = qSession::get($type, []);
        if (!in_array($message, $aMessage)) {
            $aMessage[] = $message;
        }
        qSession::set($type, $aMessage);
    }

    public static function display(): string
    {
        return '<?= qMessage::displayRender() ?>';
    }

    public static function displayRender(): string
    {
        $table = [];
        foreach (['info', 'success', 'warning', 'error'] as $type) {
            $mess = qSession::getAndDel('message-'.$type, []);
            if (is_array($mess) && count($mess) > 0) {
                $table[$type] = $mess;
            }
        }
        if (count($table) > 0) {
            $block = new qTemplate();
            $block->messages = $table;
            return $block->render('message/block');
        }
        return '';
    }
}
