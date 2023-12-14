<template>
    <div class="h-screen">
        <navigation />
        <div class="container mx-auto">
            <div class="mx-5 mb-2 mt-5 px-2.5 pb-2.5 bg-white rounded">
                <va-data-table
                id="data-table"
                :items="studentsList"
                :columns="scope.tblColumns"
                :per-page="$root.config.tblPerPage"
                :current-page="$root.config.tblCurrPage"
                no-data-html="No student(s) to show"
                animated
                >
                    <template #headerAppend>
                        <tr class="table-crud__slot">
                            <th
                            v-for="key in Object.keys(createStudent.data)"
                            :key="key"
                            class="py-1 pr-1"
                            >
                                <va-button
                                v-if="key.includes('student_id')"
                                preset="primary"
                                icon="post_add"
                                @click="createStudent.modal = !createStudent.modal"
                                >
                                    Add
                                </va-button>
                            </th>
                        </tr>
                    </template>

                    <template #cell(name)="{ value }">
                        {{ value }}
                    </template>
                    <template #cell(services)="{ row, isExpanded }">
                        <va-button
                        class="w-full"
                        preset="secondary"
                        size="small"
                        :icon="isExpanded ? 'va-arrow-up': 'va-arrow-down'"
                        @click="row.toggleRowDetails(), activePreviewRow = row"
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
                            :model-value="rowData.services_name"
                            placeholder="No services available"
                            readonly
                            autosize
                            outline
                            />
                        </div>
                    </template>
                    <template #cell(date_enrolled)="{ value }">
                        {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
                    </template>
                    <template #cell(student_id)="{ rowData }">
                        <va-button
                        class="mb-2 mr-2 hover:opacity-[0.65!important]"
                        title="Edit"
                        preset="plain"
                        icon="edit"
                        @click="editStudent.data = { ...rowData }, editStudent.modal = !editStudent.modal"
                        />
                        <va-button
                        class="mb-2 mr-2 hover:opacity-[0.65!important]"
                        title="Delete"
                        preset="plain"
                        icon="delete"
                        @click="editStudent.data = { ...rowData }, editStudent.deleteModal = !editStudent.deleteModal"
                        />
                    </template>

                    <template #bodyAppend>
                        <tr v-if="$root.tblPagination(studentsList)">
                            <td
                            id="pagination"
                            :colspan="scope.tblColumns.length"
                            >
                                <div class="flex pt-[10px] select-none">
                                    <va-pagination
                                    class="justify-center"
                                    v-model="$root.config.tblCurrPage"
                                    :pages="$root.tblPagination(studentsList)"
                                    input
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </va-data-table>
            </div>
            <div class="mx-5 mb-5">
                <div class="va-title text-neutral-400">
                    Input fields with
                    <span class="text-sm leading-[0.25rem]">*</span>
                    are required
                </div>
            </div>
        </div>
    </div>
    <va-modal
    v-model="createStudent.modal"
    @cancel="createStudent.data = createStudent.resetData, createStudent.serviceEmpty = false, createStudent.studentEmpty = false"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Add Student
                </div>
                <va-select
                    v-model="createStudent.data.student_id"
                    class="mb-2 w-full"
                    label="Student"
                    requiredMark
                    noOptionsText="No students available"
                    :options="studentsList"
                    :error="createStudent.studentEmpty"
                    :error-messages="'The student field is required.'"
                    text-by="name"
                    value-by="student_id"
                />
                <va-select
                    v-model="createStudent.data.service_id"
                    class="mb-2 w-full"
                    label="Service"
                    requiredMark
                    multiple
                    noOptionsText="No services available"
                    :options="services"
                    :error="createStudent.serviceEmpty"
                    :error-messages="'The service field is required.'"
                    text-by="name"
                    value-by="id"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="createStudent.data = createStudent.resetData, createStudent.serviceEmpty = false, createStudent.studentEmpty = false, createStudent.modal = !createStudent.modal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        @click="insertUpdateStudent('create')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editStudent.modal"
    @cancel="editStudent.data = {}, editStudent.serviceEmpty = false, editStudent.studentEmpty = false"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Edit Student
                </div>
                <va-select
                    v-model="editStudent.data.student_id"
                    class="mb-2 w-full"
                    label="Student"
                    requiredMark
                    noOptionsText="No students available"
                    :options="studentsList"
                    :error="editStudent.studentEmpty"
                    :error-messages="'The student field is required.'"
                    text-by="name"
                    value-by="student_id"
                />
                <va-select
                    v-model="editStudent.data.service_id"
                    class="mb-2 w-full"
                    label="Service"
                    requiredMark
                    noOptionsText="No services available"
                    :options="services"
                    :error="editStudent.serviceEmpty"
                    :error-messages="'The service field is required.'"
                    text-by="name"
                    value-by="id"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editStudent.data = {}, editStudent.serviceEmpty = false, editStudent.studentEmpty = false, editStudent.modal = !editStudent.modal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        @click="insertUpdateStudent('save')"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="editStudent.deleteModal"
    @cancel="editStudent.data = {}"
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Archive Student
                </div>
                <va-alert color="warning">
                    <template #icon>
                        <va-icon name="warning" />
                    </template>
                    This action is irreversible
                </va-alert>
                <va-input
                type="textarea"
                :model-value="editStudent.data.name"
                class="w-full mb-2 force-noresize"
                readonly
                autosize
                />
                <va-select
                    v-model="editStudent.data.service_id"
                    class="mb-2 w-full"
                    label="Service"
                    multiple
                    requiredMark
                    noOptionsText="No services available"
                    :options="services"
                    :error="editStudent.serviceEmpty"
                    :error-messages="'The service field is required.'"
                    text-by="name"
                    value-by="id"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="editStudent.data = {}, editStudent.deleteModal = !editStudent.deleteModal"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        @click="deleteStudent"
                        >
                            <p class="font-normal">Confirm</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<style lang="scss" scoped>
.table-crud {
    --va-form-element-default-width: 0;

    .va-input {
        display: block;
    }

    &__slot {
        th {
            vertical-align: middle;
        }
    }
}
</style>

<script>
import navmenu from '@/components/navbar.vue';
import formatDate from '@/functions/formatdate.js';

const newStudent = {
    date_enrolled: formatDate(new Date().getTime(), 'YYYY-MM-DD HH:mm:ss'),
    name: "",
    service_id: null,
    student_id: null,
};

export default {
    components: {
        navigation: navmenu
    },
    data () {
        const scope = {
            tblColumns: [
                { key: "name", label: "Student", sortable: true },
                { key: "services", label: "Services", width: 80, tdAlign: "center", sortable: false },
                { key: "date_enrolled", label: "Date Enrolled", width: 125, sortable: true },
                { key: "student_id", label: "Action", width: 60, sortable: false },
            ]
        };

        return {
            scope,
            services: [],
            studentsList: [],
            student: {},
            createStudent: {
                modal: false,
                serviceEmpty: false,
                studentEmpty: false,
                resetData: { ...newStudent },
                data: { ...newStudent }
            },
            editStudent: {
                modal: false,
                deleteModal: false,
                serviceEmpty: false,
                studentEmpty: false,
                data: {}
            },
            activePreviewRow: null
        };
    },
    mounted () {
        this.getStudents();
    },
    methods: {
        deleteStudent() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/enrolled_student/delete',
                data: { id: this.editStudent.data.student_id, services: this.editStudent.data.service_id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.editStudent.data = {};
                    this.editStudent.deleteModal = false;

                    this.getStudents();

                    this.activePreviewRow && this.activePreviewRow.toggleRowDetails(false);
                    this.activePreviewRow = null;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        insertUpdateStudent(method) {
            if (method !== 'create' || method !== 'save') {
                var data = method === 'create' ? this.createStudent.data : (method === 'save' && this.editStudent.data);
                data.is_edit = method === 'save' ? true : false;
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/enrolled_student/save',
                    data: data
                }).then(response => {
                    if (response.data.status == 1) {
                        this.$root.prompt(response.data.text);
                        method === 'create' ? (
                            this.createStudent.data = { ...newStudent },
                            this.createStudent.modal = false,
                            this.createStudent.nameEmpty = false
                        )
                        : (
                            method === 'save' && (
                                this.editStudent.data = {},
                                this.editStudent.modal = false,
                                this.editStudent.nameEmpty = false
                            )
                        );

                        this.getStudents();
                    } else this.$root.prompt(response.data.text);
                }).catch(error => {
                    this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'name').length) {
                        method === 'create' ? this.createStudent.nameEmpty = true
                        : (
                            method === 'save' && (this.editStudent.nameEmpty = true)
                        );
                    }
                });
            } else this.$root.prompt();
        },
        getStudents() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/get_enrolled_students'
            }).then(response => {
                if (response.data.status == 1) {
                    this.studentsList = response.data.result.students;
                    this.services = response.data.result.services;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
