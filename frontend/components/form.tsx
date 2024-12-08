"use client";

import React, { useEffect } from "react";
import { useForm, Controller, FieldValues } from "react-hook-form";
import { Input } from "./ui/input";
import { Button } from "./ui/button";
import { DatePicker } from "./ui/datepicker";
import { filterChangedFormFields } from "@/lib/utils";

type Field = {
  label: string;
  rules: string[];
  type: string;
};

type Schema = {
  [key: string]: Field;
};

type DynamicFormProps = {
  schema: Schema;
  onSubmit: (data: FieldValues) => void;
  is_enabled: boolean;
  values?: any;
  formType: "add" | "edit";
};

const DynamicForm: React.FC<DynamicFormProps> = ({
  schema,
  onSubmit,
  is_enabled,
  values,
  formType,
}) => {
  const {
    setValue,
    control,
    handleSubmit,
    formState: { errors, dirtyFields },
  } = useForm<FieldValues>({
    mode: "onChange",
    defaultValues: Object.keys(schema).reduce((acc, key) => {
      acc[key] = "";
      return acc;
    }, {} as Record<string, string>),
  });
  useEffect(() => {
    // Set form values only once after the component mounts
    if (values) {
      Object.entries(values).forEach(([key, value]) => {
        setValue(key, value);
      });
    }
  }, [values, setValue]);

  const handleFormSubmit = (data: FieldValues) => {
    if (formType === "edit") {
      // Filter only the changed fields for "edit"
      const changedValues = filterChangedFormFields(data, dirtyFields);
      onSubmit(changedValues);
    } else {
      // Send all values for "add"
      onSubmit(data);
    }
  };

  const renderField = (name: string, field: Field) => {
    const validationRules: Record<string, any> = {};

    field.rules.forEach((rule) => {
      if (rule.startsWith("required")) {
        validationRules.required = `${field.label} is required`;
      } else if (rule.startsWith("max_length")) {
        const max = rule.match(/max_length\[(\d+)\]/)?.[1];
        if (max) {
          validationRules.maxLength = {
            value: parseInt(max, 10),
            message: `${field.label} cannot exceed ${max} characters`,
          };
        }
      } else if (rule.startsWith("min_length")) {
        const min = rule.match(/min_length\[(\d+)\]/)?.[1];
        if (min) {
          validationRules.minLength = {
            value: parseInt(min, 10),
            message: `${field.label} must be at least ${min} characters`,
          };
        }
      } else if (rule.startsWith("regex_match")) {
        const regex = rule.match(/regex_match\[(.*)\]/)?.[1];
        if (regex) {
          validationRules.pattern = {
            value: new RegExp(regex),
            message: `Invalid ${field.label}`,
          };
        }
      }
    });

    switch (field.type) {
      case "input":
        return (
          <div className="flex flex-col w-full" key={name}>
            <label>{field.label}</label>
            <Controller
              name={name}
              control={control}
              rules={validationRules}
              render={({ field: controllerField }) => (
                <Input {...controllerField} type="input" />
              )}
            />
            {errors[name] && (
              <p className="error-message" style={{ color: "red" }}>
                {errors[name].message?.toString()}
              </p>
            )}
          </div>
        );
      case "date":
        return (
          <div className="flex flex-col w-full" key={name}>
            <label>{field.label}</label>
            <Controller
              name={name}
              control={control}
              rules={validationRules}
              render={({ field: controllerField }) => (
                <DatePicker
                  selectedDate={controllerField.value}
                  onDateChange={(newDate: Date | undefined) =>
                    controllerField.onChange(newDate)
                  }
                />
              )}
            />
            {errors[name] && (
              <p className="error-message" style={{ color: "red" }}>
                {errors[name].message?.toString()}
              </p>
            )}
          </div>
        );

      default:
        return (
          <div key={name}>
            <p className="error-message" style={{ color: "red" }}>
              Unsupported field type: {field.type}
            </p>
          </div>
        );
    }
  };

  return (
    <form
      className="flex flex-col gap-4"
      onSubmit={handleSubmit(handleFormSubmit)}
    >
      <div className="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-4">
        {Object.entries(schema).map(([name, field]) =>
          renderField(name, field)
        )}
      </div>
      <div>
        <Button disabled={!is_enabled} type="submit">
          Submit
        </Button>
      </div>
    </form>
  );
};

export default DynamicForm;
