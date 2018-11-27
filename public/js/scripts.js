$(document).on('click', '.tasks__list tr', function() {
    var task_id = $(this).data('task');
    showTask(task_id);
});

function showTask(task_id) {
    var url = '/api/v1/task/' + task_id;
    var tasks = getTasksList();
    if(tasks[task_id] == undefined) {
        $.ajax({
            url: url,
            dataType: 'json'
        }).done(function(task_data) {
            tasks[task_id] = task_data;
            setTasksList(tasks);
            showTaskModal(task_data);
        });
    } else {
        task_data = tasks[task_id];
        showTaskModal(task_data);
    }
    
}
function showTaskModal(task_data) {
    $.each(task_data, function(key, value){
        $('.task [data-key='+key+']').text(value);
    });
    $.fancybox.open({
        src  : '.task',
        type : 'inline',
    });
}

function getTasksList() {
    return JSON.parse(localStorage.getItem('tasks_list'));
}

function setTasksList(data) {
    localStorage.setItem('tasks_list', JSON.stringify(data));
}