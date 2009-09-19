<?php

require_once (LIBS . 'model' . DS . 'datasources' . DS . 'dbo' . DS . 'dbo_mysql.php');

class DboMysqlEx extends DboMysql {
    var $description = "MySQL DBO Driver - Extended";


    // Override showLog to colourise SQL with geshi
    function showLog($sorted = false) {
        if ($sorted) {
            $log = sortByKey($this->_queriesLog, 'took', 'desc', SORT_NUMERIC);
        } else {
            $log = $this->_queriesLog;
        }

        if ($this->_queriesCnt > 1) {
            $text = 'queries';
        } else {
            $text = 'query';
        }

        if (php_sapi_name() != 'cli') {

            App::import('Vendor', 'Geshi');
            $geshi = new GeSHi('', 'mysql');
            $geshi->set_header_type(GESHI_HEADER_DIV);
            $geshi->enable_keyword_links(false);
            
            print ("<table class=\"cake-sql-log\" id=\"cakeSqlLog_" . preg_replace('/[^A-Za-z0-9_]/', '_', uniqid(time(), true)) . "\" summary=\"Cake SQL Log\" cellspacing=\"0\" border = \"0\">\n<caption>{$this->_queriesCnt} {$text} took {$this->_queriesTime} ms</caption>\n");
            print ("<thead>\n<tr><th>Nr</th><th>Query</th><th>Error</th><th>Affected</th><th>Num. rows</th><th>Took (ms)</th></tr>\n</thead>\n<tbody>\n");

            foreach ($log as $k => $i) {
                $geshi->set_source($i['query']);
                $query = $geshi->parse_code();
                print ("<tr><td>" . ($k + 1) . "</td><td>" . $query . "</td><td>{$i['error']}</td><td style = \"text-align: right\">{$i['affected']}</td><td style = \"text-align: right\">{$i['numRows']}</td><td style = \"text-align: right\">{$i['took']}</td></tr>\n");
                print ("<tr><td colspan=\"6\"><hr/></td></tr>\n");
            }
            print ("</tbody></table>\n");
        } else {
            foreach ($log as $k => $i) {
                print (($k + 1) . ". {$i['query']} {$i['error']}\n");
            }
        }
    }
    
    // Override logQuery to add backtrace info for each SQL query, as appended comments
    function logQuery($sql) {
        
        $trace = debug_backtrace();
        
        
        
        $logEntry = false;
        if (!preg_match('#^DESCRIBE #i', $sql)) {
            foreach ($trace as $step) {
                if (!empty($step['object']) && !empty($step['file']) && !empty($step['type'])) {
                    if (preg_match('#' . preg_quote('cake' . DS . 'dispatcher.php') . '$#', $step['file'])) {
                        break;
                    }
                    $file = implode(DS, array_slice(explode(DS, $step['file']), -3));
                    //if (preg_match('#_controller.php$#', $file)) {
                    if ($logEntry) {
                        /*
                        foreach ($trace as $i => $t) { unset($trace[$i]['object']); }
                        debug($trace);
                        exit;
                        */
                        
                        $function = $step['function'];
                        
                        if ($step['class'] == 'Model' && $step['type'] == '->' && $step['function'] == 'find') {
                            if (isset($step['args'][0]) && is_string($step['args'][0])) {
                                $function .= '(\'' . $step['args'][0] . '\')';
                            }
                        }
                        
                        $sql .= "\n-- ".sprintf(
                            '%s - %s , [%s] %s%s%s',
                            $file,
                            $step['line'],
                            $step['class'],
                            get_class($step['object']),
                            $step['type'],
                            $function
                        );
                        
                        
                        
                        //$sql .= "\n-- [".$step['type']."]" . $step['class'] . ' '. get_class($step['object']) . ' ' . $step['function'] . ' - ' . $file . ' - ' . $step['line'];
                        //break;
                    }
                    if (preg_match('#' . preg_quote('model' . DS . 'model.php') . '$#', $step['file'])) {
                        $logEntry = true;
                    }
                }
            }
        }
        
        $ret = parent::logQuery($sql);
        
        //$this->log($this->_queriesLog[count($this->_queriesLog)-1], 'sql');
        
        return $ret;
    }
}

?>