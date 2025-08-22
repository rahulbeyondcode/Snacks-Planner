import React from "react";

import ConfirmationModal from "shared/components/modals/confirmation-modal";
import InfoModal from "shared/components/modals/info-modal";

import { useModalStore } from "shared/components/modals/store";

const RenderModals: React.FC = () => {
  const { confirmAction, infoModal } = useModalStore();

  return (
    <>
      {confirmAction.isVisible && <ConfirmationModal />}
      {infoModal.isVisible && <InfoModal />}
    </>
  );
};

export default RenderModals;
