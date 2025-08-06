import type {
  CategoryFormDataType,
  CategoryType,
} from "features/manage-settings/helpers/manage-setting-types";
import React, { useEffect, useState } from "react";
import Modal from "shared/components/modal";

type CategoryModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: CategoryFormDataType) => void;
  editData?: CategoryType | null;
};

const CategoryModal: React.FC<CategoryModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const [formData, setFormData] = useState<CategoryFormDataType>({
    name: "",
  });

  useEffect(() => {
    if (editData) {
      setFormData({
        name: editData.name,
      });
    } else {
      setFormData({
        name: "",
      });
    }
  }, [editData, isOpen]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.name.trim()) {
      onSave(formData);
      onClose();
    }
  };

  const handleChange = (field: keyof CategoryFormDataType, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Category" : "Add Category"}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        {/* Category Name */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Category Name *
          </label>
          <input
            type="text"
            value={formData.name}
            onChange={(e) => handleChange("name", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter category name"
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

export default CategoryModal;
