const task = document.getElementById("task");
const add = document.getElementById("add");
const taskList = document.getElementById("taskList");

function addTask() {
    const taskValue = task.value.trim();
    if (taskValue === "") {
        alert("Please enter a task.");
        return;
    }

    const li = document.createElement("li");
    const taskspan = document.createElement("span");
    taskspan.textContent = taskValue;
    li.appendChild(taskspan);

    taskspan.addEventListener("click", function () {
        taskspan.classList.toggle("completed");
    });

    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Delete";
    deleteButton.addEventListener("click", function () {
        li.remove();
    });
    li.appendChild(deleteButton);

    taskList.appendChild(li);
    task.value = "";
}
add.addEventListener("click", addTask);
task.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        addTask();
    }
});
