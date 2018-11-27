<?php

    namespace Tochka;

class Task
{
    private $tasks;

    function __construct($number = 1000)
    {
        $this->generateTasks($number);
    }

    public function generateTasks($number)
    {
        $cache = new Cache();

        if($cache->get('tasks')) {
            $this->tasks = $cache->get('tasks');
        } else {
            $tasks = [];
            for ($i=1; $i <= $number; $i++) {
                $tasks[] = [
                    'id' => $i,
                    'title' => 'title' . $i,
                    'date' => date("d.m.Y H:i",mktime($i, 0)),
                    'author' => 'author' . $i,
                    'status' => 'status' . $i,
                    'description' => 'description' . $i
                ];
            }
            $this->tasks = $tasks;
            $cache->set('tasks', $this->getAllTasks());
            $this->cache = false;
        }
    }

    public function getAllTasks()
    {
        return $this->tasks;
    }

    public function getTasks($offset = NULL, $limit = 10, $search = '')
    {
        $tasks = $this->getAllTasks();
        $tasks_list = [];
        foreach ($tasks as $task) {
            $tasks_list[] = [
                'id' => $task['id'],
                'title' =>  $task['title'],
                'date' =>  $task['date']
            ];
        }
        if($search) {
            $tasks_list = array_filter($tasks_list, function($task) use ($search) {
                return strrpos($task['title'], $search) !== false;
            });
        }
        if($offset !== NULL) {
            $tasks_list = array_slice($tasks_list, $offset, $limit);
        }
        return $tasks_list;
    }

    public function getTask($id)
    {
        $tasks = $this->getAllTasks();
        $index = array_search($id, array_column($tasks, 'id'));
        if($index !== false) {
            return $tasks[$index];
        } else {
            return 'No task';
        }

    }

}