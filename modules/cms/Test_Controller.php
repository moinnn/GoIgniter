<?php
namespace Modules\Cms;

use \Unit_test;

class Test_Controller extends \CI_Controller
{

    private $_tests = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('inflector');
        $this->unit = new \Unit_test();

        // Run all class with "test" prefix
        foreach(get_class_methods($this) as $method)
        {
            if(substr($method,0,4) == 'test')
            {
                $this->_tests[] = $method;
            }
        }
    }

    public function global_setup_and_tearDown()
    {
        // run once before and after all the test
    }

    public function global_setup()
    {
        // run once before all the test
    }

    public function global_tearDown()
    {
        // run once after all the test
    }

    public function setup_and_tearDown()
    {
        // run before and after each test
    }

    public function setup()
    {
        // run before each test
    }

    public function tearDown()
    {
        // run after each test
    }

    public function index()
    {

        $this->global_setup_and_tearDown();
        $this->global_setup();
        $separator = array();
        foreach($this->_tests as $test)
        {
            $this->setup_and_tearDown();
            $this->setup();

            // get result count
            $result = $this->unit->result();
            $result_count = count($result);
            $separator[$result_count] = humanize($test);

            if(method_exists($this, $test))
            {
                $this->{$test}();
            }

            $this->tearDown();
            $this->setup_and_tearDown();
        }
        $this->global_tearDown();
        $this->global_setup_and_tearDown();

        // create the result test
        $result = $this->unit->results;
        $result_count = count($result);
        $passed_count = 0;
        $failed_count = 0;

        // merge separator as header, and  count passed/failed count
        for($i=0; $i<count($result); $i++)
        {
            // add Header based on separator
            $result[$i]['header'] = array_key_exists($i, $separator)?
                $separator[$i] : '';

            // truncate Test & Expected value if necessary
            $rowTest = $result[$i]['test'];
            $rowExpected = $result[$i]['expected'];
            if($rowTest == $rowExpected && strlen($rowTest) > 60)
            {
                if(strpos($rowExpected, '<') === FALSE && strpos($rowExpected, '<') === FALSE)
                {
                    $truncated = substr($rowTest, 0, 56).' ...';
                    $result[$i]['test'] = $truncated;
                    $result[$i]['expected'] = $truncated;
                }
            }

            // calculate passed & failed count
            if($result[$i]['result'] == 'passed')
            {
                $passed_count ++;
            }
            else
            {
                $failed_count ++;
            }
        }

        // get general informations
        $memory_usage = $this->benchmark->memory_usage();
        $elapsed_time = $this->benchmark->elapsed_time();

        // get db queries and query times
        $queries = array();
        $total_queries = 0;
        $total_query_time = 0;
        if(isset($this->db))
        {
            for($i=0; $i<count($this->db->queries); $i++)
            {
                $queries[] = array(
                    'sql' => $this->db->queries[$i],
                    'time' => $this->db->query_times[$i]
                );
                $total_queries ++;
                $total_query_time += $this->db->query_times[$i];
            }
        }

        // send to view
        $data = array(
            'tests' => $result,
            'total_tests' => $result_count,
            'passed_tests' => $passed_count,
            'failed_tests' => $failed_count,
            'queries' => $queries,
            'total_queries' => $total_queries,
            'total_query_time' => $total_query_time,
            'memory_usage' => $memory_usage,
            'elapsed_time' => $elapsed_time,
        );

        view('cms/test_controller_index', $data);
    }

}
