<?php

class Logs_Logs {
    
    
    public static function factory()
    {
            return new Logs_Logs();
    }
    
    public function filter_array( $logs, $logs_query ) {
        
        $filter_from    = date('Y-m-d', strtotime($logs_query['logs_from']));
        $filter_to      = date('Y-m-d', strtotime($logs_query['logs_to']));
                
        foreach ($logs as $logs_key => $log) {
            foreach ( $log['content'] as $key => $content) {
                
                $log_date = date('Y-m-d', strtotime($content['date']));

                if( $content['type'] !== $logs_query['logs_type'] || !(($log_date >= $filter_from) && ($log_date <= $filter_to)) ) {
                    unset($logs[$logs_key]['content'][$key]);
                }
            }
        }
        return $logs;
    }
    
    
    public function create_dir_array() {
        
        $dirs = array();
        $return_files = array();
        $logdir = APPPATH . '/logs/' . preg_replace('/[^a-z0-9\-\_\.]+/', '-', strtolower($_SERVER['HTTP_HOST'])) . '/';
        if ($dir = @opendir($f = $logdir)) {
            while($file = readdir($dir)) {
                
            if(!in_array($file, array('.','..','.DS_Store'))) {

               //echo $file.'<br>';

               if ($dir1 = @opendir($f1 = $logdir.$file.'/')) {
                    while($file1 = readdir($dir1)) {

                        if(!in_array($file1, array('.','..','.DS_Store'))) {

                        //echo '-'.$file1.'<br>';

                            if ($dir2 = @opendir($f2 = $logdir.$file.'/'.$file1.'/')) {
                                while($file2 = readdir($dir2)) {

                                    if(!in_array($file2, array('.','..','.DS_Store'))) {

                                    //echo '--'.$file2.'<br>';

                                        if ($dir3 = @opendir($f3 = $logdir.$file.'/'.$file1.'/'.$file2.'/')) {
                                            while($file3 = readdir($dir3)) {

                                                if(!in_array($file3, array('.','..','.DS_Store'))) {

                                                //echo '---'.$file3.'<br>';

                                                $dirs[$file][$file1][$file2]=$file3;

                                                $return_files[] = array(

                                                    'title' => $file.'/'.$file1.'/'.$file2.'/'.$file3,
                                                    'content' => Logs_Core::parse($logdir.$file.'/'.$file1.'/'.$file2.'/'.$file3));
                                                }
                                            }  
                                            closedir($dir3);
                                        } else {

                                            echo 'File doesn\'t exist ('.$f3.')';
                                        }
                                    }
                                }  
                                closedir($dir2);
                            } else {

                                echo 'File doesn\'t exist ('.$f2.')';

                            }
                        }
                    }  
                    closedir($dir1);
                } else {

                    echo 'File doesn\'t exist ('.$f1.')';
                }
            }
            }  
            closedir($dir);
        } else {
            
            echo 'File doesn\'t exist ('.$f.')';    
        }
        sort($return_files);
        return array_reverse($return_files);
    }
}
