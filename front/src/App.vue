<script setup>
import { onMounted, ref } from 'vue';

const tarefas = ref([])
const tarefa = ref('')

onMounted(() => {
  carregar()
});

function carregar() {
  fetch('http://localhost:8080/get_tasks.php')
  .then(response => response.json())
  .then(json => {
    tarefas.value = json
  })
}

function salvar() {
  if (tarefa.value == '') return;
  const form = new FormData();
  form.append('title', tarefa.value);

  fetch('http://localhost:8080/add_task.php', {
    method: 'POST',
    body: form
  }).then(() => {
    tarefa.value = ''
    carregar()
  })
}

function remove(task) {
  const form = new FormData();
  form.append('id', task.id);
  
  fetch('http://localhost:8080/delete_task.php', {
    method: 'POST',
    body: form
  }).then(() => {
    carregar()
  })
}

function complete(task) {
  const form = new FormData();
  form.append('id', task.id);
  
  fetch('http://localhost:8080/complete_task.php', {
    method: 'POST',
    body: form
  }).then(() => {
    carregar()
  })
}
</script>

<template>
  <h1>Lista de tarefas</h1>
  <ul>
    <li v-for="task in tarefas" :key="task.id">
      <span :class="{completed: task.completed}">{{ task.title }}</span> 
      <div>
        <a v-if="task.completed == 0" @click.prevent="complete(task)">complete</a> <a v-else @click.prevent="remove(task)">delete</a>
      </div>
    </li>
  </ul>
  <form @submit.prevent="salvar">
    <input type="text" v-model="tarefa">
    <button>salvar</button>
  </form>
</template>

<style scoped>
a {
  cursor: pointer;
}

li {
  display: flex;
  justify-content: space-between;
  list-style: none;
}
li > span {
  font-weight: bold;
}

.completed {
  text-decoration: line-through;
}
</style>
