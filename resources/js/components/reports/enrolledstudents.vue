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
            <template #cell(enrollment_id)="{ rowData }">
                {{ rowData.enrollment_id }}
            </template>
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
            <template #cell(certificate_status)="{ rowData }">
                {{ rowData.certificate_status }}
            </template>
            <template #cell(id)="{ rowData }">
                <va-button
                class="mb-2 mr-2 hover:opacity-[0.65!important]"
                title="Edit"
                preset="plain"
                icon="edit"
                @click="
                    editService.data = { ...rowData },
                    editService.modal = true
                "/>
                 </template>
        </va-data-table>
    </div>

    <va-modal
    v-model="editService.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Update LTO Data
                </div>
                <va-date-input
                v-model="editService.data.ltms"
                requiredMark
                label="LTMS"
                />

                <va-date-input
                v-model="editService.data.aces"
                requiredMark
                label="ACES"
                />

                <va-input
                v-model="editService.data.ccm"
                label="Certificate Control Number"
                requiredMark
                class="w-full mb-2"
                />


                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editService.modal = false">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"

                        @click="editService.saved = true, insertUpdateService('save'), editService.isEditLoading = true, editService.modal = false"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>



<script>
import formatDate from '@/functions/formatdate.js';

export default {
    data () {
        const record = {
            tblColumns: [
                { key: "enrollment_id", label: "EnrollmentID", sortable: true },
                { key: "student_name", label: "Student", sortable: true },
                { key: "gender", label: "Gender", sortable: true },
                { key: "hours", label: "Hours", sortable: true },
                { key: "transmission", label: "Clutch", sortable: true },
                { key: "date_start", label: "Attendance", sortable: true },
                { key: "instructor", label: "Instructor", sortable: true },
                { key: "certificate_status", label: "Certificate Status", sortable: true },
                { key: "id", label: "Action", width: 60, sortable: false }
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
            editService: {
                modal: false,
                isEditLoading: false,
                deleteModal: false,
                price: {
                    err: 0,
                    empty: false,
                },
                durationEmpty: false,
                roomIdEmpty: false,
                vehicleIdEmpty: false,
                requirementsEmpty: false,
                fileOverSizeLimit: false,
                fileNotFound: false,
                saved: false,
                data: {}
            },
        };
    },
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
        insertUpdateService(method) {
            if (method !== 'create' || method !== 'save') {
                let fdata;
                fdata = this.editService.data
                const data = new FormData();
                data.append('form', JSON.stringify(fdata));
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/report/update',
                    data: data,
                    headers: { 'Content-Type': 'multipart/form-data' }
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createService.data = { ...newServ },
                            this.createService.modal = false,
                            this.createService.saved = false
                        )
                        : (method === 'save' && (
                                this.editService.data = { ...newServ },
                                this.editService.modal = false,
                                this.editService.saved = false
                            )
                        );
                        this.editService.isEditLoading = false;
                        this.getServices();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    this.$root.prompt(error.response.data.message);
                    this.editService.isEditLoading = false;
                });
            } else this.$root.prompt();
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
    },
    props: ['mode'],
}
</script>
