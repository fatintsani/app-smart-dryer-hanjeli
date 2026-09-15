import { reactive } from 'vue';
import { t } from '../i18n';

export const dialogState = reactive({
  isOpen: false,
  title: '',
  message: '',
  details: '',
  type: 'danger', // 'danger' | 'warning' | 'info' | 'success'
  confirmText: '',
  cancelText: '',
  showCancel: true,
  resolve: null,
});

export const confirmDialog = ({
  title,
  message,
  details = '',
  type = 'danger',
  confirmText,
  cancelText,
} = {}) => {
  return new Promise((resolve) => {
    dialogState.title = title || t('modals.confirmTitle', 'Konfirmasi Tindakan');
    dialogState.message = message || t('modals.confirmDesc', 'Apakah Anda yakin ingin melanjutkan tindakan ini?');
    dialogState.details = details;
    dialogState.type = type;
    dialogState.confirmText = confirmText || t('modals.yesConfirm', 'Ya, Lanjutkan');
    dialogState.cancelText = cancelText || t('modals.noCancel', 'Batal');
    dialogState.showCancel = true;
    dialogState.resolve = resolve;
    dialogState.isOpen = true;
  });
};

export const alertDialog = ({
  title,
  message = '',
  details = '',
  type = 'info',
  confirmText,
} = {}) => {
  return new Promise((resolve) => {
    dialogState.title = title || t('common.info', 'Informasi Sistem');
    dialogState.message = message;
    dialogState.details = details;
    dialogState.type = type;
    dialogState.confirmText = confirmText || t('common.close', 'Tutup');
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
