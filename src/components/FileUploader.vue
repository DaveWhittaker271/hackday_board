<template>
  <q-uploader
    url="/webapi/files/endpoint/"
    :accept="accepts"
    :headers="getHeaders()"
    :multiple="multiple"
    class="full-width"
    style="height: 200px"
    @uploaded="uploaded"
    @failed="failed"
  />
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'FileUploader',
  props: {
    accepts: String,
    multiple: Boolean
  },
  methods: {
    getHeaders() {
      const token = localStorage.getItem('authToken');
      const headers = [];

      if (token) {
        headers.push({name: 'Authorization', value: 'Bearer ' + token});
      }

      return headers;
    },
    uploaded() {
      this.$emit('uploaded');
    },
    failed() {
      this.$emit(upload-failed);
    }
  }
})
</script>
