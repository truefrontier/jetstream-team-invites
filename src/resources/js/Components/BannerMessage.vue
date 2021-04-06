<template>
  <div v-if="message" class="alert alert-info">
    {{ message }}
  </div>
</template>

<script>
export default {
  props: ['message'],
};
</script>