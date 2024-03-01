This component has the same API as the `el-drawer` component (https://element.eleme.io/#/en-US/component/drawer) and incorporates accessibility features

<template>
  <focus-trap v-if="localVisible">
    <el-drawer v-bind="$attrs" v-on="$listeners" :visible.sync="localVisible">
      <template v-slot:title>
        <h2 v-if="title" class="h4">{{ title }}</h2>
      </template>
      <slot></slot>
    </el-drawer>
  </focus-trap>
</template>

<script>
import FocusTrap from './FocusTrap.vue';

export default {
  name: 'AccessibleDrawer',
  components: {
    FocusTrap,
  },
  props: {
    visible: {
      type: Boolean,
      required: true
    },
    title: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      // local data to avoid mutating prop
      localVisible: this.visible,
    };
  },
  watch: {
    visible(newValue) {
      this.localVisible = newValue;
    },
    localVisible(newValue) {
      this.$emit('update:visible', newValue);
    },
  }
};
</script>
