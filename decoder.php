<?php

namespace By {

    /**
     * @param string $encodedPassword encoded password from winscp.ini or system registry [HKEY_CURRENT_USER\Software\Martin Prikryl\WinSCP 2\Sessions\SESSION NAME\Password]
     * @param string $username
     * @param string $host
     * @return string decoded password
     */
    function decryptPassword($encodedPassword, $username, $host)
    {
        $result = '';
        $twoBytes = substr($encodedPassword, 0, 4);

        if ($twoBytes === 'A35C')
            $encodedPassword = substr($encodedPassword, 4);

        $arPassword = str_split(pack('H*', $encodedPassword));

        $length = 0;
        $start = 0;

        if (isset($arPassword[1])) {
            $length = decryptChar($arPassword[0]);
            $start = decryptChar($arPassword[1]) * 2 + 2;
        }

        for ($i = $start; $i < $length; $i++)
            $result .= chr(decryptChar($arPassword[$i]));

        if ($twoBytes === 'A35C')
            $result = str_replace($username . $host, '', $result);

        return $result;
    }

    /**
     * @param string $char encoded character
     * @return int
     */
    function decryptChar($char)
    {
        return (int)~(ord($char) ^ 0xA3) & 0xFF;
    }
}