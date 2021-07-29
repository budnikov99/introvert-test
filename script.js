function getContacts(page, limit) {
    return $.ajax({
        url: '/api/v4/contacts',
        method: 'GET',
        data: {
            limit: limit,
            with: 'leads',
            page: page
        }
    });
}

function getTasks(page, limit){
    return $.ajax({
        url: '/api/v4/tasks',
        method: 'GET',
        data: {
            limit: limit,
            page: page
        }
    });
}

function putTasks(tasks){
    return $.ajax({
        url: '/api/v4/tasks',
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(tasks)
    });
}

/**
 * Возвращает относительную дату
 * @param {int} days количество дней
 * @returns дату через days дней от сегодняшнего дня
 */
function getRelativeDate(days){
    let date = new Date();
    date.setDate(date.getDate()+days);
    return date;
}

function addTasksToLeadlessContacts(){
    getContacts(1,25).done((data) => {
        let tasksToAdd = data._embedded.contacts
        .filter((contact) => contact._embedded.leads.length == 0)
        .map((contact) => {return {
            text: "Контакт без сделок", 
            complete_till: getRelativeDate(5),
            entity_id: contact.id,
            entity_type: "contact"
        }});

        putTasks(tasksToAdd).done((data) => {
            $("#result").text("Добавлено "+data._embedded.tasks.length+" задач.");
        }).fail((data) => {
            console.log(data);
            $("#result").text("Произошла ошибка.");
        });
    });
}

function checkAddedTasks(){
    getTasks().done((data) => {
        $("#result").text("");
        data._embedded.tasks.forEach((task) => {
            $("<p/>").text(task.id+": "+task.text+" (до "+new Date(task.complete_till)+")").appendTo("#result");
        });
    });
}
