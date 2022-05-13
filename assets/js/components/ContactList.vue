<template>
  <div>
    <b-table striped hover :items="contactInfo" :fields="fields"></b-table>
  </div>
</template>

<script>

import ContactService from "../app/Service/ContactService";

export default {

  data() {
    return{
      fields: ['name', 'email', 'gender', 'content'],
      contactInfo: []
    };
  },

  created() {
    this.getList();
  },

  methods: {

    getList() {
      ContactService.getAll()
          .then(response => this.contactInfo = response.data)
          .catch(error => this.contactInfo = error.data);
    },

    formatGender(gender) {
      return gender.charAt(0).toUpperCase() + gender.slice(1);
    },
  },
}
</script>