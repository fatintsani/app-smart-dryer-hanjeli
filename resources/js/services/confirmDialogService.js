import { reactive } from 'vue';

export const dialogState = reactive({
  isOpen: false,
  title: 'Konfirmasi Tindakan',
  message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
  details: '',
  type: 'danger', // 'danger' | 'warning' | 'info' | 'success'
  confirmText: 'Ya, Lanjutkan',
  cancelText: 'Batal',
  showCancel: true,
  resolve: null,
});

export const confirmDialog = ({
  title = 'Konfirmasi Tindakan',
  message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
  details = '',
  type = 'danger',
  confirmText = 'Ya, Lanjutkan',
  cancelText = 'Batal',
} = {}) => {
  return new Promise((resolve) => {
    dialogState.title = title;
    dialogState.message = message;
    dialogState.details = details;
    dialogState.type = type;
    dialogState.confirmText = confirmText;
    dialogState.cancelText = cancelText;
    dialogState.showCancel = true;
    dialogState.resolve = resolve;
    dialogState.isOpen = true;
  });
};

export const alertDialog = ({
  title = 'Informasi Sistem',
  message = '',
  details = '',
  type = 'info',
  confirmText = 'Mengerti',
} = {}) => {
  return new Promise((resolve) => {
    dialogState.title = title;
    dialogState.message = message;
    dialogState.details = details;
    dialogState.type = type;
    dialogState.confirmText = confirmText;
    dialogState.cancelText = '';
    dialogState.showCancel = false;
    dialogState.resolve = resolve;
    dialogState.isOpen = true;
  });
};

export const closeDialog = (result = false) => {
  if (dialogState.resolve) {
    dialogState.resolve(result);
  }
  dialogState.isOpen = false;
  dialogState.resolve = null;
};
