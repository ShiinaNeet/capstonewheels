<template>
    <div class="mx-5 mb-2 px-2.5 pb-2.5 bg-white rounded">
        <va-data-table
        id="data-table"
        :items="auditTrail"
        :columns="auds.tblColumns"
        :per-page="$root.config.tblPerPage"
        :current-page="$root.config.tblCurrPage"
        no-data-html="No audit log(s) to show"
        :filter="filter"
        @filtered="filtered = $event.items"
        animated
        >
            <template #headerAppend>
                <tr class="table-crud__slot">
                    <th
                    class="py-1 pr-1"
                    colspan="2"
                    >
                        <va-input
                        v-model="filter"
                        placeholder="Search..."
                        >
                            <template #appendInner>
                                <va-icon name="search" color="#C2C2C2" />
                            </template>
                        </va-input>
                    </th>
                    <th
                    v-for="key in Object.keys(auds.tblColumns).slice(0, 3)"
                    :key="key"
                    class="py-1 pr-1"
                    >
                        <va-button
                        v-if="key.includes(5)"
                        class="invisible"
                        preset="primary"
                        disabled
                        />
                    </th>
                </tr>
            </template>
            <template #cell(w)="{ value }">
                <P>qwewqe</P>
            </template>
            <template #cell(category)="{ value }">
                {{
                    auds.auditCategory[
                        $root.arrayFind(
                            auds.auditCategory, (item) => item.value === parseInt(value)
                        )
                    ].label
                }}
            </template>
            <template #cell(email)="{ value }">
                <p v-if="value">{{ value }}</p>
                <p
                v-if="!value"
                class="
                    w-[132px] rounded-[2px] bg-[var(--va-primary)]
                    px-[5px] pt-[5px] pb-[4px]
                    text-[14px] text-white text-center
                "
                >
                    <pre>SYSTEM GENERATED</pre>
                </p>
            </template>
            <template #cell(action_description)="{ row, isExpanded }">
                
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
                    :model-value="rowData.action_description"
                    placeholder="No description available"
                    readonly
                    autosize
                    outline
                    />
                </div>
            </template>
            <template #cell(action_type)="{ value }">
                <va-badge

                :text="
                    value &&
                    (
                        auds.auditAction[
                            $root.arrayFind(
                                auds.auditAction, (item) => item.value === parseInt(value)
                            )
                        ].label
                    )
                "
                :color="
                    value &&
                    (
                        auds.auditAction[
                            $root.arrayFind(
                                auds.auditAction, (item) => item.value === parseInt(value)
                            )
                        ].color
                    )
                "
                />
            </template>
            <template #cell(created_at)="{ value }">
                {{ formatDate(value, 'MMM. Do YYYY', 'Invalid Date') }}
            </template>

            <template #bodyAppend>
                <tr v-if="$root.tblPagination(auditTrail)">
                    <td
                    id="pagination"
                    :colspan="auds.tblColumns.length"
                    >
                        <div class="flex pt-[10px] select-none">
                            <va-pagination
                            class="justify-center"
                            v-model="$root.config.tblCurrPage"
                            :pages="filter == '' ? $root.tblPagination(auditTrail) : (pages, $root.config.tblCurrPage = 1)"
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
        const auds = {
            tblColumns: [
                { key: "category", label: "Audit", width: 100, sortable: true },
                { key: "email", label: "Account", width: "100%", sortable: true },
                { key: "action_type", label: "Action", width: 50, sortable: true },
                { key: "action_description", label: "Description", width: 80, tdAlign: "center", sortable: false },
                { key: "created_at", label: "Created On", sortable: true }
            ],
            auditCategory: [
                { label: "Account", value: 0 },
                { label: "News", value: 1 },
                { label: "Help", value: 2 },
                { label: "Service", value: 3 },
                { label: "Schedule", value: 4 },
                { label: "Requirement", value: 5 },
                { label: "Room", value: 6 },
                { label: "Vehicle", value: 7 },
                { label: "Database", value: 8 },
                { label: "Enrollment", value: 9 },
                { label: "Payment", value: 10 },
                { label: "Report", value: 11 },
                { label: "Inquiry", value: 12 }
            ],
            auditAction: [
                { label: "Create", color: "success", value: 0 },
                { label: "Update", color: "warning", value: 1 },
                { label: "Delete", color: "danger", value: 2 },
                { label: "Notice", color: "secondary", value: 2 }
            ],
        };

        return {
            auds,
            auditTrail: [],
            activePreviewRow: null,
            filtered: null,
            filter: ""
        };
    },
    computed: {
        pages() {
            return this.$root.config.tblPerPage && this.$root.config.tblPerPage !== 0
            ? Math.ceil(this.filtered.length / this.$root.config.tblPerPage)
            : this.filtered.length;
        },
    },
    mounted () {
        this.getAuditTrail();
    },
    methods: {
        getAuditTrail() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/audit_trail/get'
            }).then(response => {
                if (response.data.status == 1) {
                    this.auditTrail = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        formatDate
    }
}
</script>
