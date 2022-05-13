<template>
  <nav class="navbar navbar-expand-lg navbar-primary">
    <router-link to="/" class="navbar-brand">Social Places</router-link>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <router-link to="/" class="nav-link">
            <font-awesome-icon icon="home" />Dashboard
          </router-link>
        </li>
        <li v-if="showContactList" class="nav-item">
          <router-link to="/contacts" class="nav-link">
            <font-awesome-icon icon="users" />Contacts
          </router-link>
        </li>
      </ul>
    </div>

    <div v-if="!currentUser" class="navbar-nav ml-auto">
      <ul class="navbar-nav">
        <li class="nav-item">
          <router-link to="/register" class="nav-link">
            <font-awesome-icon icon="user-plus" />Sign Up
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/login" class="nav-link">
            <font-awesome-icon icon="sign-in-alt" />Login
          </router-link>
        </li>
      </ul>

    </div>
    <div v-if="currentUser" class="navbar-nav ml-auto">
      <ul class="navbar-nav">
        <li class="nav-item">
          <router-link to="/profile" class="nav-link">
            <font-awesome-icon icon="user" />
            {{ currentUser.username }}
          </router-link>
        </li>
        <li class="nav-item">
          <a class="nav-link" href @click.prevent="logOut">
            <font-awesome-icon icon="sign-out-alt" />LogOut
          </a>
        </li>
      </ul>
    </div>
  </nav>
</template>
<script>
export default {
  name: 'navbar',

  computed: {
    currentUser() {
      return this.$store.state.auth.user;
    },

    showContactList() {
      return !!this.currentUser;

    },
  },
  methods: {
    logOut() {
      this.$store.dispatch('auth/logout');
      this.$router.push('/login');
    }
  }

}
</script>
<style>
@import "https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css";
</style>