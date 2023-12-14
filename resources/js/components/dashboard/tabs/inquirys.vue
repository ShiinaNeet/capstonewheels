<template>
    <div id="hmenu">
        <div class="flex">
            <div
            id="t-search"
            class="shrink mr-2"
            >
                <div class="max-w-[210px!important]">
                    <va-input
                    v-model="filter"
                    placeholder="Search..."
                    >
                        <template #appendInner>
                            <va-icon name="search" color="#C2C2C2" size="20px" />
                        </template>
                    </va-input>
                </div>
            </div>
            <div>
                <va-tabs
                v-model="stab"
                @update:modelValue="
                    filtered = null,
                    filter = ''
                ">
                    <template #tabs>
                        <va-tab
                        v-for="stab in stabs"
                        :key="stab"
                        >
                            <va-icon
                            :name="stab.icon"
                            class="mr-2"
                            size="small"
                            />
                                <div class="va-title">
                                    {{ stab.title }}
                                </div>
                        </va-tab>
                    </template>
                </va-tabs>
            </div>
        </div>

        <va-divider class="m-[0!important]" />

        <va-data-table
        id="data-table"
        v-if="stabs[stab].title === 'Entries'"
        :items="inquiry.submitted.items"
        :columns="inquiry.submitted.table.columns"
        :per-page="inquiry.submitted.table.perPage"
        :current-page="inquiry.submitted.table.currPage"
        no-data-html="No inquiry(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #cell(subject)="{ value }">
                {{ value }}
            </template>
            <template #cell(email)="{ value }">
                {{ value }}
            </template>
            <template #cell(inquiry)="{ row, isExpanded }">
                <va-button
                class="w-full"
                preset="secondary"
                size="small"
                :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                @click="row.toggleRowDetails(), inquiry.submitted.activePreviewRow = row"
                >
                    {{ !isExpanded ? 'Show': 'Hide' }}
                </va-button>
            </template>
            <template #expandableRow="{ rowData }">
                <div
                class="p-2"
                id="table-row-desc"
                >
                    <va-input
                    type="textarea"
                    :model-value="rowData.inquiry"
                    placeholder="No description available"
                    readonly
                    autosize
                    outline
                    />
                </div>
            </template>
            <template #cell(created_at)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(inquiry.submitted.items)">
                    <td
                    id="pagination"
                    :colspan="inquiry.submitted.table.columns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="inquiry.submitted.table.currPage"
                            :pages="filter == '' ? $root.tblPagination(inquiry.submitted.items) : (pages, inquiry.submitted.table.currPage = 1)"
                            input
                            />
                        </div>
                    </td>
                </tr>
            </template>
        </va-data-table>
    </div>

</template>

<script>
import formatDate from '@/functions/formatdate.js';

export default {
    data () {
        const inq = {
            tblColumns: [
                { key: "subject", label: "Subject", width: 350, sortable: true },
                { key: "email", label: "E-mail", width: 200, sortable: true },
                { key: "inquiry", label: "Inquiry", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Submitted On", width: 125, sortable: true },
            ],
        };

        return {
            inquiry: {
                submitted: {
                    table: {
                        columns: inq.tblColumns,
                        perPage: this.$root.config.tblPerPage,
                        currPage: this.$root.config.tblCurrPage,
                    },
                    items: [],
                    activePreviewRow: null,
                },
            },
            filtered: null,
            filter: "",
            stabs: [
                { title: 'Entries', icon: 'login' },
            ],
            stab: 0,
        }
    },
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        this.getInquiries();
    },
    methods: {
        getInquiries() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/get_inquiries',
            }).then(response => {
                if (response.data.status == 1) {
                    this.inquiry.submitted.items = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    },
}
</script>
