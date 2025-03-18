import LoadingSpinner from "@/Components/LoadingSpinner.jsx"
export default function PrimaryButton({
    className = '',
    disabled,
    children,
    loading = false,
    ...props
}) {
    return (
        <button
            {...props}
            className={
                `inline-flex items-center rounded-md  bg-sky-700 px-4 py-2 text-xs font-semibold tracking-widest text-white transition duration-150 ease-in-out hover:bg-sky-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 ${
                    disabled && 'opacity-25'
                } ` + className
            }
            disabled={disabled}
        >
            {loading && <span><LoadingSpinner></LoadingSpinner></span>}
            {!loading && children}
        </button>
    );
}
