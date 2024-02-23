<template>
  <h4>
    GigaDB: Uploading files for the dataset {{ identifier }}
  </h4>
</template>

<script>
export default {
  props: {
    identifier: {
      type: String,
      required: true,
    }
  }
}
</script>