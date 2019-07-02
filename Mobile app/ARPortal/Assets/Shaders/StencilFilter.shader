Shader "Custom/StencilFilter"
{
	Properties
	{
		_Color ("Color", Color) = (1,1,1,1)
	}
	
	SubShader
	{
        Color [_Color]
        Stencil
        {
            Ref 1
            Comp [_StencilTest]
        }
        
		Pass
		{
			
		}
	}
}
