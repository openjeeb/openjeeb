<?php

App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));

class PersianDateBehavior extends ModelBehavior {

    /**
     * Empty Setup Function
     */
    function setup(&$model) {
        $this->model = $model;
    }

    /**
     * Function which handle the convertion of the date to and from Persian date
     * @param array $data data array from and to database
     * @param int $direction with 2 possible values '1' determine that data is going to database, '2' determine that data is pulled from database
     * @return array converted array;
     * @access restricted
     */
    function _convertDate($data, $direction) {
        //just return false if the data var is false
        if ($data == false) {
            return false;
        }
        //result model
        foreach ($data as $key => $value) {
            if ($direction == 2) {
                foreach ($value as $key1 => $value1) {
                    if ($this->model->name == $key1) { //if it's current model;
                        $columns = $this->model->getColumnTypes();
                    } else {
                        //Fix for loading models on the fly
                        if (isset($this->model->{$key1})) {
                            $columns = $this->model->{$key1}->getColumnTypes();
                        } else {
                            if (!in_array($key1,array('Parent','_Token'))) {
                                App::import('Model', $key1);
                                if(class_exists($key1)){
                                    $model_on_the_fly = new $key1();
                                    $columns = $model_on_the_fly->getColumnTypes();
                                }
                            } else {
                                $columns=null;
                            }   
                        }
                    }
                    foreach ($value1 as $k => $val) {
                        if (!is_array($val) AND !empty ($columns)) {
                            if (in_array($k, array_keys($columns))) {
                                if ($columns[$k] == 'date' || $columns[$k] == 'datetime') {
                                    if ($val == '0000-00-00' || $val == '0000-00-00 00:00:00' || $val == '') { //also clear the empty 0000-00-00 values
                                        $data[$key][$key1][$k] = null;
                                    } else {
                                        $persianDate = new PersianDate();
                                        if ($columns[$k] == 'date') {
                                            $data[$key][$key1][$k] = $persianDate->pdate_format($val, $this->model->convertDateFormat);
                                        } elseif ($columns[$k] == 'datetime') {
                                            $data[$key][$key1][$k] = $persianDate->pdate_format($val, $this->model->convertDateTimeFormat);
                                        }
                                    }
                                }
                            }
                        }else {
                            foreach ($val as $k2=>$val2) {
                                if (in_array($k2, array_keys($columns))) {
                                    if ($columns[$k2] == 'date' || $columns[$k2] == 'datetime') {
                                        if ($val2 == '0000-00-00' || $val2 == '0000-00-00 00:00:00' || $val2 == '') { //also clear the empty 0000-00-00 values
                                            $data[$key][$key1][$k2] = null;
                                        } else {
                                            $persianDate = new PersianDate();
                                            if ($columns[$k2] == 'date') {
                                                $data[$key][$key1][$k][$k2] = $persianDate->pdate_format($val2, $this->model->convertDateFormat);
                                            } elseif ($columns[$k2] == 'datetime') {
                                                $data[$key][$key1][$k][$k2] = $persianDate->pdate_format($val2, 'H:i '.$this->model->convertDateFormat);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if ($this->model->name == $key) { //if it's current model;
                    $columns = $this->model->getColumnTypes();
                } else {
                    //Fix for loading models on the fly
                    if (isset($this->model->{$key})) {
                        $columns = $this->model->{$key}->getColumnTypes();
                    } else {
                        if (!in_array($key,array('Parent','_Token')) ) {
                            App::import('Model', $key);
                            $model_on_the_fly = new $key();
                            $columns = $model_on_the_fly->getColumnTypes();
                        } else {
                            $columns=null;
                        }
                    }
                }
                foreach ($value as $k => $val) {
                    if (!is_array($val) AND !empty($columns)) {
                        if (in_array($k, array_keys($columns))) {
                            if ($columns[$k] == 'date' || $columns[$k] == 'datetime') {
                                if ($val == '0000-00-00' || $val == '0000-00-00 00:00:00' || $val == '') { //also clear the empty 0000-00-00 values
                                    $data[$key][$k] = null;
                                } else {
                                    $persianDate = new PersianDate();
                                    $data[$key][$k] = $persianDate->pdate_format_reverse($val);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    function beforeValidate($model) {
        if ($model->inputConvertDate) {
            $model->data = $this->_convertDate($model->data, 1); //direction is from interface to database
        }
        return true;
    }

    function afterFind(&$model, $results) {
        if ($model->outputConvertDate) {
            $results = $this->_convertDate($results, 2); //direction is from database to interface
        }
        return $results;
    }

    function pDate(&$model, $datetime, $format='j F Y') {
        $persianDate = new PersianDate();
        return $persianDate->pdate_format($datetime, $format);
    }

    function pDateReverse(&$model, $datetime) {
        $persianDate = new PersianDate();
        return $persianDate->pdate_format_reverse($datetime);
    }

}

?>