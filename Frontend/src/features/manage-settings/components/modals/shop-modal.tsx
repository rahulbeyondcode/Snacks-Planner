import { PAYMENT_MODES as paymentModes } from "features/manage-settings/helpers/constants";
import type {
  ShopFormDataType,
  ShopType,
} from "features/manage-settings/helpers/manage-setting-types";
import React, { useEffect, useState } from "react";
import Modal from "shared/components/modal";

type ShopModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: ShopFormDataType) => void;
  editData?: ShopType | null;
};

const ShopModal: React.FC<ShopModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const [formData, setFormData] = useState<ShopFormDataType>({
    name: "",
    address: "",
    contactDetails: "",
    paymentMode: "",
    notes: "",
  });

  useEffect(() => {
    if (editData) {
      setFormData({
        name: editData.name,
        address: editData.address,
        contactDetails: editData.contactDetails,
        paymentMode: editData.paymentMode,
        notes: editData.notes,
      });
    } else {
      setFormData({
        name: "",
        address: "",
        contactDetails: "",
        paymentMode: "",
        notes: "",
      });
    }
  }, [editData, isOpen]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.name.trim() && formData.paymentMode.trim()) {
      onSave(formData);
      onClose();
    }
  };

  const handleChange = (field: keyof ShopFormDataType, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Shop" : "Add Shop"}
      className="max-w-lg"
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        {/* Shop Name */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Shop Name *
          </label>
          <input
            type="text"
            value={formData.name}
            onChange={(e) => handleChange("name", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter shop name"
            required
          />
        </div>

        {/* Shop Address */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Shop Address
          </label>
          <input
            type="text"
            value={formData.address}
            onChange={(e) => handleChange("address", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter shop address"
          />
        </div>

        {/* Contact Details */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Contact Details
          </label>
          <input
            type="text"
            value={formData.contactDetails}
            onChange={(e) => handleChange("contactDetails", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter contact details"
          />
        </div>

        {/* Payment Mode */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Payment Mode *
          </label>
          <select
            value={formData.paymentMode}
            onChange={(e) => handleChange("paymentMode", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            required
          >
            <option value="">Select payment mode</option>
            {paymentModes.map((mode) => (
              <option key={mode} value={mode}>
                {mode}
              </option>
            ))}
          </select>
        </div>

        {/* Notes */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Notes
          </label>
          <textarea
            value={formData.notes}
            onChange={(e) => handleChange("notes", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500 resize-vertical"
            placeholder="Enter any additional notes"
            rows={3}
          />
        </div>

        {/* Submit Button */}
        <div className="flex justify-end pt-4">
          <button
            type="submit"
            className="px-6 py-2 bg-blue-500 text-white rounded font-handwriting hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Save
          </button>
        </div>
      </form>
    </Modal>
  );
};

export default ShopModal;
