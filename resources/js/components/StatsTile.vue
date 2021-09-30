<template>
    <div class="w-full">
        <div class="shadow-lg rounded p-4 border"
             :class="['tile-' + color]">
            <h4>{{ title }}</h4>
            <div>{{ numbers }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        cases: {
            type: Number,
            required: true
        },
        color: {
            type: String,
        }
    },

    computed: {
        numbers() {
            return this.cases.toString().toSeparatedNumber();
        }
    }
}
</script>
