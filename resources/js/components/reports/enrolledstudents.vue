<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <div class="container mx-auto py-3">
            <div class="flex">
                <div class="mr-2 max-w-[165px]">
                    <va-date-input
                    v-model="dateFilter.date"
                    v-model:type="dateFilter.type"
                    label="Month"
                    :format="(date) => formatDate(date, 'MMMM, YYYY')"
                    @click:month="getList(false)"
                    />
                </div>
                <div class="mr-2 max-w-[165px]">
                    <va-select
                    v-model="service"
                    label="Service"
                    :options="services"
                    text-by="name"
                    value-by="id"
                    searchable
                    highlight-matched-text
                    :disabled="!services.length ? true : false"
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
                    <va-checkbox
                    v-model="excludeColumns"
                    label="Exclude certificate columns"
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
            <template #cell(gender)="{ value }">
                {{ value == 1 ? "Male" : "Female" }}
            </template>
            <template #cell(transmission)="{ value }">
                {{ value == "-" ? "" : (value == 0 ? "Automatic" : "Manual") }}
            </template>
            <template #cell(date_start)="{ rowData }">
                {{ formatDate(rowData.date_start, 'MMMM Do', 'Invalid Date') }} - {{ formatDate(rowData.date_end, 'MMMM Do', 'Invalid Date') }}
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

export default {
    data () {
        const record = {
            tblColumns: [
                { key: "student_name", label: "Student", sortable: true },
                { key: "gender", label: "Gender", sortable: true },
                { key: "hours", label: "Hours", sortable: true },
                { key: "transmission", label: "Clutch", sortable: true },
                { key: "date_start", label: "Attendance", sortable: true },
                { key: "instructor", label: "Instructor", sortable: true },
            ],
        };

        return {
            record,
            dateFilter: {
                date: new Date(),
                type: "month",
            },
            pdf: false,
            file: {
                saved: false,
            },
            excludeColumns: false,
            services: [],
            service: null,
            list: [],
        };
    },
    props: ['mode'],
    mounted() {
        var $this = this;
        $this.getServices();
        setTimeout(() => {
            if ($this.services.length > 0) {
                $this.service = $this.services[0].id;
                $this.getList(false);
            }
        }, 500);
    },
    methods: {
        getList(isExport) {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/report/enrolled_students',
                data: {
                    export: isExport,
                    failed_enrollments: this.mode ? true : false,
                    exclude_columns: this.excludeColumns,
                    month: this.formatDate(this.dateFilter.date, 'YYYY-MM-DD'),
                    service: this.service,
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
                    } else this.list = response.data.result;
                    this.file.saved = false;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        getServices() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/report/services',
            }).then(response => {
                if (response.data.status == 1) {
                    this.services = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
