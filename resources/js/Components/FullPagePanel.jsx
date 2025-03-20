export default function FullPagePanel ({title, children, className}) {

    return (
        <>
            <div className={"mt-6 bg-white rounded-md border border-gray-200 shadow " + className}>
                <div className="px-3 sm:px-4 md:p-8 py-4 border-b border-dashed border-gray-300">
                    {title}
                </div>
                <div className="p-2 sm:p-4 md:p-8 bg-gray-50 rounded-b-md">
                    {children}
                </div>
            </div>
        </>
    );
}
