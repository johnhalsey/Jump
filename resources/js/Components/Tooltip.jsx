export default function Tooltip ({text, children}) {

    return (
        <>
            <div className={'group relative inline-block'}>
                {children}
                <div className={'hidden group-hover:block absolute bg-gray-800 text-white text-center py-1 ' +
                    'min-w-[120px] left-1/2 mt-3 ml-[-60px] px-3 rounded z-[60] ' +
                    'after:content-[" "] after:absolute after:bottom-full after:left-1/2 after:ml-[-6px] ' +
                    'after:border-8 after:border after:border-t-transparent after:border-r-transparent ' +
                    'after:border-b-gray-800 after:border-l-transparent'
                }>
                <span className={'text-sm'}>{text}</span>
                </div>
            </div>
        </>
    );
}
