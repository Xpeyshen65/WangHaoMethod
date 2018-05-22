<?php

class Functional
{
    private $strSource = '';
    private $arrShow = array();

    function searchSolve($strSource) {
        $arrSymb = array('(', ')', ',');
        $arrStr = array();
        $strProcess = $this->andToComma($strSource);
        $strProcess = str_ireplace($arrSymb, '', $strProcess);
        $strProcess = $this->logicReduction($strProcess);
        $i = 0; $solve = false; $where = 'left'; $remNode = NULL;
        $rootVisited = false;

        $obj = new BinaryTree();
        $obj->insert($strProcess, '');
        $mainRoot = $obj->getRoot();
        while (!$solve) {
            $arrStr = $this->divideOR($obj->getRootValue()); // divide expressinon on 2 string, 4 rule, method Van Hao
            if (!$arrStr[0]) {
                if ($this->checkEquPart($obj->getRootValue())) { //check successful solve
                    // show chain solve
                    $arrShow = $this->getArrShow();
                    foreach ($arrShow as $value) {
                        echo $this->formatExpression($value) . '<br>';
                    }
                    return 1; //return successful code
                }
                $obj->setRoot($obj->getPrev());
                $arrShow = $this->getArrShow();
                array_pop($arrShow); // delete last element
                $this->setArrShow($arrShow);
                if ($obj->getRoot() == $mainRoot) $rootVisited = true;

                if ($where == 'left') {
                    $obj->setRoot($obj->getRight());
                    $arrShow = $this->getArrShow();
                    $arrShow[] = $obj->getRootValue();
                    $this->setArrShow($arrShow);
                    $where = 'right';
                } elseif ($where == 'right') {
                    while ($obj->getLeft() != $remNode) {
                        if (($obj->getRoot() == $mainRoot) && ($rootVisited)) {
                            //echo '<br>Не выводима<br>';
                            return 0;
                        } elseif (($obj->getRoot() == $mainRoot) && (!$rootVisited)) {
                            $rootVisited = true;
                        }
                        $remNode = $obj->getRoot();
                        $obj->setRoot($obj->getPrev());
                        $arrShow = $this->getArrShow();
                        array_pop($arrShow); // delete last element
                        $this->setArrShow($arrShow);
                    }
                    if ($obj->getRoot() == $mainRoot) $rootVisited = true;
                    $obj->setRoot($obj->getRight());
                    $arrShow = $this->getArrShow();
                    $arrShow[] = $obj->getRootValue();
                    $this->setArrShow($arrShow);
                    $where = 'left';
                }
            } else{
                $obj->insert($arrStr[0], 'left');
                $obj->insert($arrStr[1], 'right');
                $obj->setRoot($obj->getLeft());
                $arrShow = $this->getArrShow();
                $arrShow[] = $obj->getRootValue();
                $this->setArrShow($arrShow);
                $where = 'left';
            }
            $i++;
        }
        return -1;
    }

    function andToComma($str) {
        $str = str_ireplace('&', ',', $str);
        return $str;
    }

    function divideOR($str) {
        //echo "<br>**IN: $str **<br>";
        $lenStr = strlen($str);
        $start = false; $returnSourceStr = false;
        for ($i = 1; $i < $lenStr; $i++) {
            if ($str[$i] == 'V') {
                $start = true;
                break 1;
            }
        }

        if ($start) {
            if (($str[1] == 'V') || (($str[2] == 'V') && ($str[0] == '!')))  {
                $str = $str . ' ';
                for ($i = $lenStr; $i > 0; $i--) {
                    $str[$i] = $str[$i-1];
                }
                $str[0] = ' ';
                $returnSourceStr = true;
                $lenStr++;
            }

            $i = 0; $endDivide = false;
            $arrStr = array('', '');
            while ($i < $lenStr) {
                //echo "$i ";
                if ($endDivide == false) {
                    if (($str[$i + 1] == '!') && ($str[$i + 3] == 'V')) {
                        $endDivide = true;
                        $arrStr[0] .= ($str[$i] . $str[$i + 1] . $str[$i + 2]);
                        if ($str[$i + 4] == '!') {
                            $arrStr[1] .= ($str[$i] . $str[$i + 4] . $str[$i + 5]);
                            $i = $i + 6;
                        } elseif ($str[$i + 4] != '!') {
                            $arrStr[1] .= ($str[$i] . $str[$i + 4]);
                            $i = $i + 5;
                        }
                    } elseif (($str[$i + 2] == 'V')) {
                        $endDivide = true;
                        $arrStr[0] .= ($str[$i] . $str[$i + 1]);
                        if ($str[$i + 3] == '!') {
                            $arrStr[1] .= ($str[$i] . $str[$i + 3] . $str[$i + 4]);
                            $i = $i + 5;
                        } elseif ($str[$i + 3] != '!') {
                            $arrStr[1] .= ($str[$i] . $str[$i + 3]);
                            $i = $i + 4;
                        }
                    }
                }
                $arrStr[0] .= ($str[$i]);
                $arrStr[1] .= ($str[$i]);
                $i++;
            }
        } else {
            $arrStr[0] = false;
            $arrStr[1] = false;
        }
        //echo "***<br>!!! 1) $arrStr[0] !!! 2) $arrStr[1]<br>";
        if (($returnSourceStr) && ($arrStr[0])) {
            $lenArrStr1 = strlen($arrStr[0]);
            $lenArrStr2 = strlen($arrStr[1]);
            for ($i = 0; $i < ($lenArrStr1-1); $i++) {
                $arrStr[0][$i] = $arrStr[0][$i+1];
            }
            for ($i = 0; $i < ($lenArrStr2-1); $i++) {
                $arrStr[1][$i] = $arrStr[1][$i+1];
            }
            $arrStr[0][$lenArrStr1-1] = '';
            $arrStr[1][$lenArrStr2-1] = '';
        }

        //echo "<br>!!! 1) $arrStr[0] !!! 2) $arrStr[1]<br>";
        return $arrStr;
    }

    function checkEquPart($str) {
        $str = $this->clearNOT($str);
        $hash = array(0, 0); $lenStr = strlen($str);
        $left = ''; $right = ''; $switch = false;
        /*if ($str[0] == ' ') {
            for ($i = 0; $i < ($lenStr-1); $i++) {
                $str[$i] = $str[$i+1];
            }
            $str[$lenStr-1] = '';
            $lenStr--;
        }*/
        for ($i = 0; $i < $lenStr; $i++) {
            if ($str[$i] == '>') {
                $switch = true;
                continue;
            }
            if (!$switch) $left .= $str[$i];
            else $right .= $str[$i];
        }
        $left = $this->clearSameVar($left);
        $right = $this->clearSameVar($right);

        for ($i = 0; $i < strlen($left); $i++) {
            $hash[0] += ord($left[$i]);
        }
        for ($i = 0; $i < strlen($right); $i++) {
            $hash[1] += ord($right[$i]);
        }
        $str = $left . '>' . $right;
        if ($hash[0] == $hash[1]) {
            $arrShow = $this->getArrShow();
            $arrShow[] = $str;
            $this->setArrShow($arrShow);
            return true;
        }
        else return false;
    }

    function clearSameVar($str) {
        $lenStr = strlen($str);
        $newStr = ''; $tmp = array();
        for ( $i = 0; $i < $lenStr; $i++)
        {
            if (in_array($str{$i}, $tmp)) continue;
            else $newStr .= ($tmp[] = $str{$i});
        }
        return $newStr;
    }

    function clearNOT($str) {
        $lenStr = strlen($str);
        $arrStr = array('', '');
        $rightPart = false;
        $i = 0; $j = 0;
        while ($i < $lenStr) {
            if ($str[$i] == '>') {
                $rightPart = true;
                $j = 1;
                $i++;
                continue;
            }
            if (($str[$i] == '!') && (!$rightPart)) {
                $arrStr[1] .= $str[$i+1];
                $i++;
            } elseif (($str[$i] == '!') && ($rightPart)) {
                $arrStr[0] .= $str[$i+1];
                $i++;
            } else {
                $arrStr[$j] .= $str[$i];
            }
            $i++;
        }
        $str = $arrStr[0] . '>' . $arrStr[1];
        return $str;
    }

    function logicReduction($str) {  // aVa => a etc
        $lenStr = strlen($str);
        $newStr = ''; $endDivide = false;
        for ($i = 0; $i < $lenStr; $i++) {
            //echo "$i) " . $newStr . '<br>';
            if ($str[$i] == '>') $endDivide = true;
            if (!$endDivide) {
                if (($str[$i] == 'V') && (($str[$i-1] == $str[$i+1]))) {
                    $i++;
                } elseif (($str[$i] == 'V') && (($str[$i-1] == $str[$i+2])) && ($str[$i-2] == '!') && ($str[$i+1] == '!')) {
                    $i = $i + 2;
                } else {
                    $newStr .= $str[$i];
                }
            } else {
                $newStr .= $str[$i];
            }
        }
        return $newStr;
    }

    function formatExpression($str) {
        $arrSymb = array('(', ')', ',');
        $arrReplace = array('', '', '');
        $str = $this->andToComma($str);
        $str = str_ireplace($arrSymb, $arrReplace, $str);
        $arrSymbChange = array('&', 'V', '>', '!');
        $arrSymbReplace = array(' ∧ ', ' ∨ ', ' → ', ' ¬');
        $lenStr = strlen($str); $newStr = '';
        for ($i = 0; $i < $lenStr; $i++) {if ($i != 0) {
                if ((!array_search($str[$i], $arrSymbChange)) && ((!array_search($str[$i - 1], $arrSymbChange)))) {
                    $newStr .= (', ' . $str[$i]);
                } elseif (($str[$i] == '!') && (!array_search($str[$i - 1], $arrSymbChange))) {
                    $newStr .= (', ' . $str[$i] . $str[$i+1]);
                    $i++;
                }
                else {
                    $newStr .= $str[$i];
                }
            } else {
                $newStr .= $str[$i];
            }
        }
        //echo "<br>NEWSTR: $newStr<br>";
        $newStr = str_ireplace($arrSymbChange, $arrSymbReplace, $newStr);
        return $newStr;
    }

    function changeHTMLtagToSymb($str) {
        $arrSymb = array('&gt;', '&amp;');
        $arrReplace = array('>', '&');
        $str = str_ireplace($arrSymb, $arrReplace, $str);
        return $str;
    }

    function upV($str) {
        $str = str_replace('v', 'V', $str);
        return $str;
    }

    //<getters|setters>
    function getStrSource() {
        return $this->strSource;
    }

    function setStrSource($value) {
        $this->strSource = $value;
    }

    function getArrShow() {
        return $this->arrShow;
    }

    function setArrShow($arrValue) {
        $this->arrShow = $arrValue;
    }
    //</getters|setters>
}