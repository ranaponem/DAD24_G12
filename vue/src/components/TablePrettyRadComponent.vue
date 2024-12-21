<template>
        <div class="flex items-center justify-center w-full mb-8">
                <table class="table-auto border-collapse w-3/4 text-center rounded-lg overflow-hidden">
                        <thead>
                                <tr class="bg-white">
                                        <th
                                                v-for="(header, index) in headers"
                                                :key="index"
                                                class="bg-primary px-4 py-2 text-xl text-stone-900"
                                                :style="{ minWidth: '150px' }"
                                        >
                                                {{ header }}
                                        </th>
                                </tr>
                        </thead>
                        <tbody>
                                <tr v-if="items.length === 0">
                                        <td
                                                :colspan="headers.length"
                                                class="px-4 py-8 text-2xl font-bold text-red-600 bg-red-100 rounded-b-lg"
                                        >
                                                {{ emptyMessage }}
                                        </td>
                                </tr>
                                <tr
                                        v-else
                                        v-for="(item, index) in items"
                                        :key="item.id"
                                        :class="index % 2 === 0 ? 'bg-white' : 'bg-stone-200'"
                                        class="transition-colors duration-200 text-black"
                                >
                                        <template v-for="(key, idx) in keysToShow " :key="idx" >
                                                <td class="px-4 py-2">
                                                        {{ formatValue(item[key]) }}
                                                </td>
                                        </template>
                                </tr>
                        </tbody>
                </table>
        </div>
</template>

<script>
export default {
        name: "DynamicTable",
        props: {
                items: {
                        type: Array,
                        required: true,
                },
                headers: {
                        type: Array,
                        required: true,
                },
                keysToShow: {
                        type: Array,
                        required: true,
                },
                emptyMessage: {
                        type: String,
                        default: "No data available!",
                },
        },
        methods: {
                formatValue(value) {
                        if (typeof value === "object" && value !== null) {
                                return value.nickname || value.name || JSON.stringify(value);
                        }
                        if (typeof value === "string" && !isNaN(Date.parse(value))) {
                                return this.formatDate(value);
                        }
                        return value || "Unknown";
                },
                formatDate(dateString) {
                        const date = new Date(dateString);
                        return date.toLocaleString("en-GB", {
                                day: "2-digit",
                                month: "short",
                                year: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                                second: "2-digit",
                                hour12: false,
                        });
                },
        },
};
</script>

<style scoped>
/* Add styles here if needed */
</style>
