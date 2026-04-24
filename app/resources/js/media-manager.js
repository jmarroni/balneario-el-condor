import Sortable from 'sortablejs';

window.mediaManager = function (config) {
    return {
        items: config.items || [],
        uploading: false,
        error: null,

        init() {
            this.$nextTick(() => {
                Sortable.create(this.$refs.grid, {
                    animation: 150,
                    onEnd: () => this.reorder(),
                });
            });
        },

        async upload() {
            const file = this.$refs.file.files[0];
            if (!file) return;
            this.uploading = true;
            this.error = null;
            const fd = new FormData();
            fd.append('file', file);
            fd.append('mediable_type', config.mediableType);
            fd.append('mediable_id', config.mediableId);
            try {
                const res = await fetch(config.storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': config.csrf,
                        Accept: 'application/json',
                    },
                    body: fd,
                });
                if (!res.ok) {
                    let msg = 'Error al subir: ' + res.status;
                    try {
                        const body = await res.json();
                        if (body && body.message) msg = body.message;
                    } catch (e) {
                        // ignore json parse issues
                    }
                    throw new Error(msg);
                }
                const data = await res.json();
                this.items.push(data);
                this.$refs.file.value = '';
            } catch (e) {
                this.error = e.message;
            } finally {
                this.uploading = false;
            }
        },

        async remove(id) {
            if (!confirm('¿Eliminar imagen?')) return;
            const url = config.destroyUrlTemplate.replace('MEDIA_ID', id);
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': config.csrf,
                    Accept: 'application/json',
                },
            });
            if (res.ok) {
                this.items = this.items.filter((i) => i.id !== id);
            } else {
                this.error = 'No se pudo eliminar la imagen.';
            }
        },

        async reorder() {
            const order = Array.from(this.$refs.grid.querySelectorAll('[data-id]'))
                .map((el, idx) => ({ id: Number(el.dataset.id), sort_order: idx }));
            const res = await fetch(config.reorderUrl, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': config.csrf,
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ items: order }),
            });
            if (res.ok) {
                this.items = order
                    .map((o) => this.items.find((i) => i.id === o.id))
                    .filter(Boolean);
            } else {
                this.error = 'No se pudo guardar el orden.';
            }
        },
    };
};
