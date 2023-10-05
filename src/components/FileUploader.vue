<template>
  <q-uploader
    url="/webapi/files/endpoint/"
    :accept="accepts"
    :headers="getHeaders()"
    :form-fields="extraFields"
    :multiple="multiple"
    class="full-width"
    style="height: 200px"
    ref="uploader"
    @added="fileAdded"
    @removed="fileRemoved"
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
    multiple: Boolean,
    extraFields: Array,
  },
  data() {
    return {
      fileCount: 0,
    }
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
      this.$emit('upload-failed');
    },
    fileAdded() {
      this.fileCount++;
    },
    fileRemoved() {
      this.fileCount--;
    },
    hasFiles() {
      return this.fileCount > 0;
    },
    upload() {
      this.$refs.uploader.upload();
    },
  }
})
</script>
