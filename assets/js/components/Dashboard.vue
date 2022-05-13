<template>
  <div class="jumbotron">
    <h1 class="display-4">Welcome!</h1>
    <p class="lead">This is a simple demo to showcase, mostly, my backend Skills for a job opportunity with Social Places.</p>
    <hr class="my-4">
    <p>To view the list of contacts in our database and/or add a new contact, click in one of the below action calls.</p>
    <router-link to="/contacts" class="btn btn-primary btn-lg" role="button">View Contacts</router-link>
    <router-link to="/contact" class="btn btn-primary btn-lg" role="button">Add Contact</router-link>
  </div>
</template>

<script>

export default {
  name: 'dashboard'
}
</script>