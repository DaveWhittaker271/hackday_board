<template>
  <q-dialog v-model="showModal" persistent transition-show="flip-down" transition-hide="flip-up">
    <q-card class="bg-primary text-white">
      <q-bar>
        <q-icon name="network_wifi" />
        <q-icon name="network_cell" />
        <q-icon name="battery_full" />
        <div>9:34</div>

        <q-space />

        <q-btn dense flat icon="close" v-close-popup @click="$emit('modal-close')">
          <q-tooltip class="bg-white text-primary">Close</q-tooltip>
        </q-btn>
      </q-bar>

      <q-card-section>
        <div class="text-h6">Alert</div>
      </q-card-section>

      <q-card-section class="q-pt-none">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Rerum repellendus sit voluptate voluptas eveniet porro. Rerum blanditiis perferendis totam, ea at omnis vel numquam exercitationem aut, natus minima, porro labore.
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: "CreateIdea",
  props: {
    showModal: Boolean
  },
}
</script>