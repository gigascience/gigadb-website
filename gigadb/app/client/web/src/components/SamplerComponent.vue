<template>
    <div id="sampler">
        <el-tag :key="sample" v-for="sample in samples" closable :disable-transitions="false" @close="handleClose(sample)">
            {{sample}}
        </el-tag>
        <el-input id="new-sample-field" class="input-new-sample" v-if="inputVisible" v-model="inputValue" ref="saveSampleInput" size="mini" @keydown.enter.native.stop="handleInputConfirm" @blur="handleInputConfirm">
        </el-input>
        <el-button v-else class="button-new-sample" size="small" @click="showInput">+ New Sample</el-button>
        <button type="button" v-on:click.prevent="saveSamples" class="btn btn-success btn-small" id="save-samples">Save</button>
    </div>
</template>
<script>
import { eventBus } from '../index.js'
export default {
    props: ['collection'],
    data() {
        return {
            samplesString: this.collection || "",
            samples: [],
            inputVisible: false,
            inputValue: ''
        };
    },
    methods: {
        handleClose(sample) {
            this.samples.splice(this.samples.indexOf(sample), 1);
        },

        showInput() {
            this.inputVisible = true;
            this.$nextTick(_ => {
                this.$refs.saveSampleInput.$refs.input.focus();
            });
        },

        handleInputConfirm() {
            let inputValue = this.inputValue;
            if (inputValue) {
                this.samples.push(inputValue);
            }
            this.inputVisible = false;
            this.inputValue = '';
        },

        saveSamples(event) {
            this.$emit("new-samples-input", this.samples)
        }
    },
    mounted: function () {
        this.$nextTick(function() {
            if(this.samplesString) {
                this.samples = this.samplesString.split(',').map(Function.prototype.call, String.prototype.trim)
            }
        })
    },
}
</script>
<style>
.el-tag+.el-tag {
    margin-left: 10px;
}

.button-new-sample {
    margin-left: 10px;
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
}

.input-new-sample {
    width: 90px;
    margin-left: 10px;
    vertical-align: bottom;
}
</style>