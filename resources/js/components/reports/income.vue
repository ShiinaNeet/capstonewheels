<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <div class="container mx-auto py-3">
            <div class="flex">
                <div class="mr-2 max-w-[260px]">
                    <va-date-input
                    v-model="dateFilter.date_range"
                    class="w-full"
                    label="Date Range"
                    :format="(date_range) => formatDate(date_range.start, 'MMM. Do YYYY', '...') + ' ~ ' + formatDate(date_range.end, 'MMM. Do YYYY', '...')"
                    @update:modelValue="getList(false)"
                    />
                </div>
                <div class="flex items-center justify-end">
                    <va-checkbox
                    v-model="pdf"
                    label="Export as PDF file"
                    class="mr-[10px] shrink"
                    :disabled="list.length == 0 || file.saved"
                    />
                    <va-button
                    class="mr-2 shrink"
                    :loading="file.saved"
                    :disabled="list.length == 0 || file.saved"
                    @click="file.saved = true, getList(true)"
                    >
                        Export
                    </va-button>
                </div>
            </div>
        </div>
        <va-data-table
        id="data-table"
        :items="list"
        :columns="record.tblColumns"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No records(s) to show"
        animated
        striped
        >
            <template #cell(amount)="{ value }">
                ₱ {{ parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </template>
            <template #cell(date_enrolled)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>
            <template #bodyAppend>
                <tr v-if="$root.tblPagination(list)">
                    <td
                    id="pagination"
                    :colspan="record.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="$root.tblPagination(list)"
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
import moment from 'moment';

export default {
    data () {
        const record = {
            tblColumns: [
                { key: "student_name", label: "Student", sortable: true },
                { key: "service", label: "Service", sortable: true },
                { key: "amount", label: "Price", sortable: true },
                { key: "date_enrolled", label: "Date Enrolled", sortable: true },
            ],
        };

        return {
            record,
            dateFilter: {
                date_range: {
                    start: moment(),
                    end: moment().add(7, 'days'),
                },
                type: "range",
            },
            pdf: false,
            file: {
                saved: false,
            },
            list: []
        };
    },
    mounted() {
        this.getList(false);
    },
    methods: {
        getList(isExport) {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/report/income',
                data: {
                    export: isExport,
                    date_range: {
                        start: this.formatDate(this.dateFilter.date_range.start, 'YYYY-MM-DD'),
                        end: this.formatDate(this.dateFilter.date_range.end, 'YYYY-MM-DD'),
                    },
                    pdf: this.pdf
                }
            }).then(response => {
                if (response.data.status == 1) {
                    if (isExport) {
                        this.$root.prompt(response.data.text);

                        const f = response.data.result.file;
                        const bin = atob(f.blob);
                        const bea = new Uint8Array(bin.length);
                        for (let i = 0; i < bin.length; i++) {
                            bea[i] = bin.charCodeAt(i);
                        }
                        const url = window.URL.createObjectURL(new Blob([bea]));
                        const a = document.createElement('a');
                        a.href = url;
                        a.setAttribute('download', f.name);
                        a.style.display = 'none';

                        document.body.appendChild(a);
                        a.click();

                        window.URL.revokeObjectURL(url);
                        a.remove();
                    } else this.list = response.data.result.list;
                    this.file.saved = false;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
