<template>
    <div class="mx-5 mb-2 px-2.5 py-2.5 bg-white rounded">
        <div class="va-title pl-2.5 pt-[0.095rem] pb-[0.185rem]">
            Database {{ mode == 0 ? 'Export' : 'Import' }}
        </div>
        <va-divider />
        <va-button
        v-if="mode == 0"
        icon="description"
        class="w-full"
        :loading="database.export.saved"
        :disabled="database.export.saved"
        @click="database.export.saved = true, bckupDatabase()"
        >
            Backup
        </va-button>
        <div v-if="mode == 1">
            <div>
                <div class="relative">
                    <label
                    class="va-title absolute left-0 top-0 right-0 px-3.5 pt-[1px] z-[1] text-[#154EC1] tracking-[0.0375rem!important]"
                    aria-hidden="true"
                    >
                        Restore
                        <span
                        v-if="database.import.saved"
                        class="float-right"
                        >
                            <va-icon
                            class="mb-[1px]"
                            name="rotate_right"
                            size="12px"
                            spin
                            />
                            Please wait...
                        </span>
                        <span class="float-right mr-1">
                            <va-icon
                            class="mb-[2px]"
                            name="upload"
                            size="11px"
                            />
                            Size Limit: None
                        </span>
                    </label>
                </div>
            </div>
            <va-file-upload
            v-model="database.import.data"
            class="mb-2"
            file-types=".sql,application/sql,application/x-sql"
            dropzone
            dropZoneText="Drop file or click here to upload"
            :disabled="database.import.saved"
            @update:modelValue="
                database.import.data.length > 1 && database.import.data.shift(),
                database.import.data.length === 1 && (database.import.saved = true, restrDatabase())
            "/>
            <va-progress-bar
            v-if="database.import.saved"
            v-model="database.import.done.percentage"
            color="success"
            :indeterminate="database.import.done.simulate"
            />
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    data () {
        return {
            database: {
                import: {
                    done: {
                        percentage: 100,
                        simulate: true,
                        delay: 2000,
                    },
                    saved: false,
                    data: [],
                },
                export: {
                    saved: false,
                    data: [],
                },
            },
            redirectToHomeDelay: 2000,
        };
    },
    props: ['mode'],
    methods: {
        bckupDatabase() {
            axios({
                method: 'GET',
                responseType: 'json',
                url: 'database/export'
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);

                    const url = window.URL.createObjectURL(new Blob([atob(response.data.blob)]));
                    const a = document.createElement('a');
                    a.href = url;
                    a.setAttribute('download', moment().format("YYYY-MM-DD_HH-mm-ss") + '_database.sql');
                    a.style.display = 'none';

                    document.body.appendChild(a);
                    a.click();

                    window.URL.revokeObjectURL(url);
                    a.remove();
                } else this.$root.prompt(response.data.text);

                this.database.export.saved = false;
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        },
        restrDatabase() {
            let dbsql;
            dbsql = this.database.import.data;

            const data = new FormData();
            (dbsql.length > 0 && dbsql[0].size !== 0) && data.append('file', dbsql[0]);

            axios({
                method: 'POST',
                type: 'JSON',
                url: '/database/import',
                data: data,
                headers: { 'Content-Type': 'multipart/form-data' }
            }).then(response => {
                setTimeout(() => {
                    this.$root.prompt(response.data.text);
                    if (response.data.status == 1) {
                        this.database.import.done.simulate = false;
                        // setTimeout(() => {
                            // this.database.import.data = [];
                            // this.database.import.done.simulate = true;
                            // this.database.import.saved = false;

                            window.location = '/';
                        // }, this.redirectToHomeDelay);
                    } else {
                        this.database.import.data = [];
                        this.database.import.done.simulate = true;
                        this.database.import.saved = false;
                    }
                }, this.database.import.done.delay);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.database.import.data = [];
                this.database.import.done.simulate = true;
                this.database.import.saved = false;
            });
        }
    }
}
</script>
