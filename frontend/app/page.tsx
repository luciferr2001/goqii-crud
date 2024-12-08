"use client"

import DynamicForm from "@/components/form";
import { FieldValues } from "react-hook-form";

export default function Home() {
  const handleSubmit = (data: FieldValues) => {
    console.log(data);
  };

  const formSchema = {
    first_name: {
      label: "First Name",
      rules: [
        "required",
        "max_length[50]",
        "regex_match[^[^\\s][\\w',.\\-\\s][^0-9_!¡?÷?¿\\/\\+=@#$%&*(){}|~<>;:[\\]]{0,}[^\\s]$]",
      ],
      type: "input",
    },
    last_name: {
      label: "Last Name",
      rules: [
        "required",
        "max_length[50]",
        "regex_match[^[^\\s][\\w',.\\-\\s][^0-9_!¡?÷?¿\\/\\+=@#$%&*(){}|~<>;:[\\]]{0,}[^\\s]$]",
      ],
      type: "input",
    },
    email: {
      label: "Email",
      rules: [
        "required",
        "regex_match[^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$]",
      ],
      type: "input",
    },
    phone_number: {
      label: "Phone Number",
      rules: [
        "required",
        "max_length[10]",
        "min_length[10]",
        "regex_match[^[0-9]+$]",
      ],
      type: "input",
    },
    dob: {
      label: "Date Of Birth",
      rules: ["required"],
      type: "date",
    },
  };
  return (
    <div className="items-center justify-items-center p-8">
      <DynamicForm schema={formSchema} onSubmit={handleSubmit} />
    </div>
  );
}
