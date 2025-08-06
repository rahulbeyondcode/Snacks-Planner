import type {
  NoSnackDayFormDataType,
  NoSnackDayType,
} from "features/manage-settings/helpers/manage-setting-types";
import React, { useEffect, useState } from "react";
import Modal from "shared/components/modal";

type NoSnackDayModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: NoSnackDayFormDataType) => void;
  editData?: NoSnackDayType | null;
};

const NoSnackDayModal: React.FC<NoSnackDayModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const [formData, setFormData] = useState<NoSnackDayFormDataType>({
    holidayName: "",
    date: "",
  });

  useEffect(() => {
    if (editData) {
      setFormData({
        holidayName: editData.holidayName,
        date: editData.date,
      });
    } else {
      setFormData({
        holidayName: "",
        date: "",
      });
    }
  }, [editData, isOpen]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.holidayName.trim() && formData.date.trim()) {
      onSave(formData);
      onClose();
    }
  };

  const handleChange = (field: keyof NoSnackDayFormDataType, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit No Snack Day" : "Add No Snack Day"}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        {/* Holiday Name */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Holiday Name *
          </label>
          <input
            type="text"
            value={formData.holidayName}
            onChange={(e) => handleChange("holidayName", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter holiday name"
            required
          />
        </div>

        {/* Date */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Date *
          </label>
          <input
            type="date"
            value={formData.date}
            onChange={(e) => handleChange("date", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            required
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

export default NoSnackDayModal;
