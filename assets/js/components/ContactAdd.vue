<template>
  <div>
    <b-container fluid>
      <b-form @submit="onSubmit" @reset="onReset" v-if="show">

        <b-row>
          <b-col sm="6">
            <b-form-group id="input-group-2" label="Your Name" label-for="input-2">
              <b-form-input
                  id="input-2"
                  v-model="form.name"
                  placeholder="Enter full names"
                  required
              ></b-form-input>
            </b-form-group>
          </b-col>
        </b-row>

        <b-row>
          <b-col sm="6">
            <b-form-group
                id="input-group-1"
                label="Email address"
                label-for="input-1"
                description="We'll never share your email with anyone else."
            >
              <b-form-input
                  id="input-1"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter email"
                  required
              ></b-form-input>
            </b-form-group>
          </b-col>
        </b-row>

        <b-row>
          <b-col sm="6">
            <b-form-group label="Gender" v-slot="{ ariaDescribedby }">
            <b-form-radio v-model="form.gender" :aria-describedby="ariaDescribedby" name="some-radios" value="female">Female</b-form-radio>
            <b-form-radio v-model="form.gender" :aria-describedby="ariaDescribedby" name="some-radios" value="male">Male</b-form-radio>
          </b-form-group>
          </b-col>
        </b-row>

        <b-row>
          <b-col sm="6">
            <b-form-group id="input-group-2" label="Content" label-for="input-2">
              <b-form-textarea
                  id="content"
                  v-model="form.content"
                  placeholder="Your message"
              ></b-form-textarea>
            </b-form-group>
          </b-col>
        </b-row>

        <b-row>
          <b-col sm="6" class="pull-right">
            <b-button type="submit" variant="primary">Submit</b-button>
            <b-button type="reset" variant="danger">Reset</b-button>
          </b-col>
        </b-row>

      </b-form>
    </b-container>
  </div>
</template>

<script>

import ContactService from "../app/Service/ContactService";

export default {
  data() {
    return {
      form: {
        email: '',
        name: '',
        gender: '',
        content: ''
      },
      show: true
    }
  },
  methods: {

    onSubmit(event) {
      event.preventDefault();

      ContactService.create(this.form)
          .then(response => {
            this.show = false;
            this.$router.push('contacts')
          })
          .catch(e => { console.log(e); });
    },

    onReset(event) {
      event.preventDefault()

      this.form.email = ''
      this.form.name = ''
      this.form.gender = ''
      this.form.content = ''
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    }
  }
}
</script>